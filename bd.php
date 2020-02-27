<?php
//Aqui esta la configuracion de la base de datos
function leer_config($nombre, $esquema){
	$config = new DOMDocument();
	$config->load($nombre);
	$res = $config->schemaValidate($esquema);
	if ($res===FALSE){ 
	   throw new InvalidArgumentException("Revise fichero de configuración");
	} 		
	$datos = simplexml_load_file($nombre);	
	$ip = $datos->xpath("//ip");
	$nombre = $datos->xpath("//nombre");
	$usu = $datos->xpath("//usuario");
	$clave = $datos->xpath("//clave");	
	$cad = sprintf("mysql:dbname=%s;host=%s", $nombre[0], $ip[0]);
	$resul = [];
	$resul[] = $cad;
	$resul[] = $usu[0];
	$resul[] = $clave[0];
	return $resul;
}

//Funcion para insertar a un nuevo usuario en la base de datos.
function insertarUsuario($usuario,$clave,$email){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
	$bd = new PDO($res[0], $res[1], $res[2]);
	
	$stmt = $bd->prepare("INSERT INTO usuarios (usuario, clave, email) VALUES('$usuario', '$clave', '$email')");
	$stmt->execute();
}

//Funcion que inserta en la tabla Recibidos la id del mensaje y su destinatario.
function insertarRecibidos($id,$destinatario){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
	$bd = new PDO($res[0], $res[1], $res[2]);

	$stmt = $bd->prepare("INSERT INTO recibidos (idMensaje, destinatario) VALUES('$id', '$destinatario')");
	$stmt->execute();

	if($stmt->rowCount() > 0){
		return true;
	}else{
		return false;
	}
}

//Funcion que permite enviar un mensaje a varios destinatarios.
function insertarMensaje($emisor,$lista_destinatarios,$asunto,$cuerpo){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
    $bd = new PDO($res[0], $res[1], $res[2]);
	
	$stmt = $bd->prepare("INSERT INTO mensajes (emisor, asunto, cuerpo) VALUES('$emisor', '$asunto', '$cuerpo')");
    
	$stmt->execute();

	if($stmt->rowCount() > 0){
		$id = $bd->lastInsertId();
		$destinatarios = explode(",", $lista_destinatarios);

		foreach ($destinatarios as $destinatario) {
			if(!insertarRecibidos($id,$destinatario)){
				return false;
			}
		}
	}
	return true;
}

//Funcion para comprobar que existe un usuario, se busca por nombre o email.
function existeUsuario($usuario,$email=null){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
	$bd = new PDO($res[0], $res[1], $res[2]);

	$stmt = $bd->prepare("SELECT * FROM usuarios WHERE usuario = :usuario or email = :email");
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':email', $email);

	$stmt->execute();
	
	return ($stmt->rowCount() == 1) ? true : false;
}

//Función que devuelve todos los usuarios registrados en la base de datos.
function cargar_usuarios(){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
    $bd = new PDO($res[0], $res[1], $res[2]);

    $stmt = $bd->prepare("SELECT usuario FROM usuarios");
    $stmt->bindParam(':usuario', $usuario);

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    return $result;
}

//Funcion que devuelve todos los datos del perfil de un usuario.
function cargar_perfil($usuario){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
	$bd = new PDO($res[0], $res[1], $res[2]);
	
	$stmt = $bd->prepare("SELECT usuario,descripcion,f_nac,ciudad,avatar FROM usuarios WHERE usuario = :usuario");
	$stmt->bindParam(':usuario', $usuario);

	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
        
    return $result;
}

//Funcion que realiza un UPDATE en la tabla recibidos, que cambia el valor de leido de false a true.
function setLeido($id,$usuario){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
	$bd = new PDO($res[0], $res[1], $res[2]);

	$stmt = $bd->prepare("UPDATE recibidos SET leido = true WHERE idMensaje = :idMensaje and destinatario = :usuario");
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':idMensaje', $id);

	$stmt->execute();
}

//Funcion que devuelve un booleano, true si el mensaje ha sido leido y false si no.
function isLeido($id,$usuario){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
		$bd = new PDO($res[0], $res[1], $res[2]);	
	
		$stmt = $bd->prepare("SELECT leido FROM recibidos WHERE idMensaje = :idMensaje and destinatario = :usuario");
		$stmt->bindParam(':usuario', $usuario);
		$stmt->bindParam(':idMensaje', $id);
	
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
	
		return $result;
}

/*Funcion que realiza una consulta a la base de datos para obtener un array con las ids de los mensajes recibidos por un usuario, el cual pasa como
parametro a la funcion que se ejecuta en el return.*/
function cargar_mensajes($usuario){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
	$bd = new PDO($res[0], $res[1], $res[2]);

	$stmt = $bd->prepare("SELECT idMensaje FROM recibidos WHERE destinatario = :usuario");
	$stmt->bindParam(':usuario', $usuario);

	$stmt->execute();

	if($stmt->rowCount() > 0){//Si se ha obtenido al menos un resultado
		//Se hace un fetchAll para guardar todas las ids devueltas por la consulta.
		$ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return mensajesRecibidos($ids,$usuario);
	}else{
		return false;
	}
}

//Funcion que devuelve los datos de un mensaje recibido
function mensajesRecibidos($ids,$usuario){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
	$bd = new PDO($res[0], $res[1], $res[2]);

	$array_mensajes = array();

	$stmt = $bd->prepare("SELECT idMensaje,emisor,asunto,fecha FROM mensajes WHERE idMensaje = :idMensaje");
	
	//Se recorre el array de ids (parametro obtenido desde la funcion "cargar_mensajes()").
	foreach($ids as $id){
		$stmt->bindParam(':idMensaje', $id['idMensaje']);
		$stmt->execute();
		//Se ejecuta la consulta preparada anteriormente por cada id y se hace un fetch a "$mensaje".
		$mensaje = $stmt->fetch(PDO::FETCH_ASSOC);
		$array_mensajes[] = array_merge($mensaje,isLeido($id['idMensaje'],$usuario));//Hacemos un merge con isLeido que coprueba si el mensaje ha sido leido o no por el destiatario.
	}

	if(empty($array_mensajes)){//Si no hay mensajes devuelve falso.
		return false;
	}else{//Si no, devuelve el array con los datos de previsualizacion de los mensajes.
		return $array_mensajes;
	}
}

//Funcion que devuelve el contenido de un mensaje recibido.
function cargar_datos_mensaje($id, $usuario){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
	$bd = new PDO($res[0], $res[1], $res[2]);

	//Consultamos si existe el mensaje con la id pasada como parametro recibidos por el usuario pasado como parametro.
	$stmt = $bd->prepare("SELECT * FROM recibidos WHERE destinatario = :usuario and idMensaje = :idMensaje");
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':idMensaje', $id);

	$stmt->execute();
	
	if($stmt->rowCount() == 1){//Si existe se realiza una consulta sobre los datos para visualizar el contenido del mensaje.
		$stmt = $bd->prepare("SELECT emisor,asunto,cuerpo,fecha FROM mensajes WHERE idMensaje = :idMensaje");
		$stmt->bindParam(':idMensaje', $id);

		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
	}
    //Se devuelve el contenido del mensaje.
    return $result;
}

function cambiarAvatar($usuario,$avatar){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
	$bd = new PDO($res[0], $res[1], $res[2]);

	$stmt = $bd->prepare("UPDATE usuarios SET avatar = :avatar WHERE usuario = :usuario");
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':avatar', $avatar);

	$stmt->execute();

	if($stmt->rowCount() > 0){//Si se ha obtenido al menos un resultado
		return true;
	}else{
		return false;
	}
}

/*Codigo comentado porque da errores.
function cambiarDescripcion($usuario,$descripcion){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
	$bd = new PDO($res[0], $res[1], $res[2]);

	$stmt = $bd->prepare("UPDATE usuarios SET descripcion = :descripcion WHERE usuario = :usuario");
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':avatar', $descripcion);

	$stmt->execute();
	if($stmt->rowCount() > 0){//Si se ha obtenido al menos un resultado
		return true;
	}else{
		return false;
	}
}

function cambiarCiudad($usuario,$ciudad){
	$res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
	$bd = new PDO($res[0], $res[1], $res[2]);

	$stmt = $bd->prepare("UPDATE usuarios SET ciudad = :ciudad WHERE usuario = :usuario");
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':avatar', $ciudad);

	$stmt->execute();
	if($stmt->rowCount() > 0){//Si se ha obtenido al menos un resultado
		return true;
	}else{
		return false;
	}
}*/