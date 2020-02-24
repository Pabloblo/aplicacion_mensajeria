//Funcion que carga el formulario de registro en la seccion "registro".
function formularioRegistro(){
    var registro = document.getElementById("registro");
    document.getElementById("login").style.display = "none";

    registro.innerHTML = '<form onsubmit="return registro()" id="formuRegistro" method="POST">'+
    'Nombre de Usuario <input id="usuario" name="usuario" type="text" required><br>'+
    'Email <input id="email" name="email" type="email" required><br>'+
    'Clave <input id="clave" name="clave" type="password" required><br>'+
    'Confirmar clave <input id="confirmarClave" name="confirmarClave" type="password" required><br>'+
    '<input type="submit" value="Confirmar">'+
    '</form>';
}

//Funcion que carga el formulario para el envio de mensajes en la seccion "enviar_mensaje".
function formularioMensaje(destinatario){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            //Se borra el contenido de las demas secciones.
            document.getElementById("bandeja_entrada").innerHTML = "";
            document.getElementById("vista_mensaje").innerHTML = "";
            document.getElementById("perfil").innerHTML = "";
            //Se guarda el elemento "enviar_mensaje" en una variable.
            var mensaje = document.getElementById("enviar_mensaje");
            //Se almacenan los nombre de los destinatarios del mensaje en una variable.
            var usuarios = JSON.parse(this.responseText);

            //Creacion del string del formulario.
            formu = '<form onsubmit="return enviarMensaje()" id="formuMensaje" method="POST">';

            //Si no se ha definido la variable destinatario se crea el input sin valor por defecto.
            if(destinatario === undefined){
                formu += '<input list="destinatarios" name="destinatarios" required><br>';
            }else{
                //En caso de que se este respondiendo a un mensaje, destinatario tendra el valor del emisor del mensaje que esta siendo respondido.
                formu += '<input list="destinatarios" name="destinatarios" value="'+ destinatario +'" required><br>';
            }
            //Se crea la lista de destinatarios posibles (todos los usuarios de la aplicacion).
            formu += '<datalist id="destinatarios">';
            for(var i = 0; i < usuarios.length; i++){
                //Se crea una opcion de la lista.
                formu += '<option value="'+ usuarios[i]['usuario'] +'"/>';
            }
            formu += '</datalist>'+
            '<input id="asunto" name="asunto" type="text"><br>'+
            '<textarea name="cuerpo" rows="20" cols="90"></textarea><br>'+
            '<input type="submit" value="Enviar">'+
            '</form>';

            mensaje.innerHTML = formu;
        }
    }
    xhttp.open("POST","usuarios_json.php",true);
    xhttp.send();
    return false;
}

//Funcion que carga la tabla con los mensajes recibidos en la seccion "bandeja_entrada".
function tablaMensajesRecibidos(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            //Se borra el contenido de las demas secciones.
            document.getElementById("enviar_mensaje").innerHTML = "";
            document.getElementById("vista_mensaje").innerHTML = "";
            document.getElementById("perfil").innerHTML = "";
            //Se guarda el elemento "bandeja_entrada" en una variable.
            var bandeja_entrada = document.getElementById("bandeja_entrada");

            //Si no hay mensajes "mensajes_recibidos_json.php" retorna "false" y se escribe el siguiente mensaje.
            if(this.responseText === "false"){
                bandeja_entrada.innerHTML = "Bandeja de entrada vacía.";
            }else{//Si hay mensajes se crea una tabla para previsualizarlos.

                //Se almacenan los datos de los mensajes en una variable.
                var mensajes = JSON.parse(this.responseText);

                //Creacion del string de la tabla.
                tabla = '<table id="tabla_mensajes_recibidos">'+
                '<tr><th>De</th><th>Asunto</th><th>Fecha</th><th>Leido</th></tr>';//cabecera de la tabla.
                for(var i = 0; i < mensajes.length; i++){
                    //Por cada mesaje se crea una fila con los campos del mensaje: "emisor", "asunto", "fecha" y "leido".
                    tabla += '<tr><td>'+ mensajes[i]['emisor'] +'</td><td><a href="#" onclick="verMensaje('+mensajes[i]['idMensaje']+')">'+ mensajes[i]['asunto'] +'</a></td><td>'+ mensajes[i]['fecha'] +'</td><td>';
                    if(mensajes[i]['leido'] == true){
                        tabla += '<img src="leido.png" alt="leido" height="20" width="20">';
                    }
                    tabla += '</td></tr>';
                }
                tabla += '</table>'
                bandeja_entrada.innerHTML = tabla;
            }
        }
    }
    xhttp.open("POST", "mensajes_recibidos_json.php", true);
    xhttp.send();
    return false;
}

//Funcion que permite visualizar el mensaje en si.
function verMensaje(id){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            //Se guarda el elemento "vista_mensaje" en una variable.
            var vista_mensaje = document.getElementById("vista_mensaje")

            //Se almacenan los datos del mensaje en cuestion.
            var mensaje = JSON.parse(this.responseText);
            //Creacion del string del mensaje.
            div = '<hr>'+
            '<div><h3>De: </h3>'+ mensaje['emisor'] +'<br>'+
            '<h3>Asunto: </h3>'+ mensaje['asunto'] +'<br>'+
            '<p>'+ mensaje['cuerpo'] +'</p><br>'+
            '<h3>Fecha: </h3>'+ mensaje['fecha'] +'<br>'+
            '<a href="#" onclick="formularioMensaje(\''+ mensaje["emisor"] +'\');">Responder</a></div>';

            vista_mensaje.innerHTML = div;
        }
    }
    //Se envia la id pasada como parametro a la funcion verMensaje() al fichero "mensaje_json.php".
    var idMensaje = id;
    var params = "idMensaje="+idMensaje;
    xhttp.open("POST", "mensaje_json.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}

//Funcion que llama a la funcion tablaPerfil y manda como parametro el valor del input "busqueda" (en cabecera_usuario.php).
function buscarPerfil(){
    //Se guarda el valor del input "busqueda" en una variable.
    var usu = document.getElementById("busqueda").value;
    var miperfil = false;
    tablaPerfil(usu, miperfil);
    return false;
}

//Funcion que carga la tabla con los datos del perfil de un usuario.
function tablaPerfil(usu,miperfil = true){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            //Se borra el contenido de las demas secciones.
            document.getElementById("enviar_mensaje").innerHTML = "";
            document.getElementById("vista_mensaje").innerHTML = "";
            document.getElementById("bandeja_entrada").innerHTML = "";
            //Se guarda el elemento "perfil" en una variable.
            var perfil = document.getElementById("perfil");
            if(this.responseText === "FALSE"){//Si no existe el usuario cuyo perfil se intenta visualizar se escribe lo siguiente.
                perfil.innerHTML = "Usuario no encontrado";
            }else{//Si existe el usuario
                //Se almacenan los datos del usuario en una variable.
                var datos = JSON.parse(this.responseText);
                //Creacion del string de la tabla con los datos del usuario, "avatar", "usuario" (nombre de usuario), "f_nac"(fecha de nacimiento), "ciudad", "descripcion".
                tabla = '<table><tr><td><img src="/aplicacion_mensajeria/usuarios/Avatares/'+ datos['avatar'] +'" width="200" height="250" alt="avatar"></td></tr>'+
                '<tr><td>'+ datos['usuario'] +'</td></tr>'+
                '<tr><td>'+ datos['f_nac'] +'</td></tr>'+
                '<tr><td>'+ datos['ciudad'] +'</td></tr>'+
                '<tr><td>'+ datos['descripcion'] +'</td></tr>';
                if(miperfil == true){
                    tabla += '<tr><td><a href="#" onclick="formularioPerfil(\''+ usu +'\')">Editar perfil</a></td></tr>';
                }
                tabla += '</table>';
                perfil.innerHTML = tabla;
            }
        }
    }
    //Se envia por el metodo POST el parametro usu (nombre de usuario) a "perfil_json.php" para cargar los datos del perfil.
    var usuario = usu;
    var params = "usuario="+usuario;
    xhttp.open("POST", "perfil_json.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}

//Funcion para cargar el formulario para editar el perfil. Cuando se enviar devuelve la retorna "actualizarPerfil()" en "sesion.js".
function formularioPerfil(usu){
    var perfil = document.getElementById("perfil");
    perfil.innerHTML = "";

    perfil.innerHTML = '<form onsubmit="return actualizarPerfil();" id="formuPerfil" method="POST">'+
    '<input type="hidden" name="usuario" id="usuario" value="'+ usu +'">'+
    'Cambiar avatar: <input type="file" name="avatar" id="avatar"><br><br>'+
    /*'Cambiar descripcion: <input type="text" name="descripcion" id="descripcion"><br><br>'+
    'Cambiar ciudad: <input type="text" name="ciudad" id="ciudad"><br><br>'+*/
    '<input type="submit" value="Actulizar"></form>';
}

//Funcion para mostrar el formulario de login.
function displayLogin(){
    if(document.getElementById("login").style.display == "none"){
        document.getElementById("registro").innerHTML = "";
        document.getElementById("login").style.display = "block";
    }
}