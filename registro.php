<?php
use PHPMailer\PHPMailer\PHPMailer;
require "vendor/autoload.php";
require "bd.php";
require "correo.php";

	function comprobarNombreUsu($usuario){
		/*la w acepta letras, numeros y "_"*/
		$r = preg_match("/^[A-Za-z]\w{5,}/", $usuario);
		if(!$r) return false;
		else return true;
    }
    
	function comprobarClave($clave){
		if (strlen($clave) < 6 or strlen($clave	) > 15) return FALSE;
		$mayu = preg_match("/[A-Z]/", $clave);
		$minu = preg_match("/[a-z]/", $clave);
		$nume = preg_match("/[0-9]/", $clave);
		$noalfa = preg_match("/[!-\\\\]/", $clave);
		return $minu and $mayu and $nume and $noalfa;
	}
	$errores = array();
	$nombre = $email = $clave1 = $clave2 ="";
	$todoBien = true;

	if ($_SERVER["REQUEST_METHOD"] == "POST") {  
		/*filtrar y validar todos los parámetros*/		
		if(empty($_POST['usuario'])){
			$errores[] = "El usuario no puede estar vacio.";
			$todoBien = false;
		}else{
			$usuario = htmlspecialchars($_POST['usuario']);
			/*hay alias pero con error*/
			if(!comprobarNombreUsu($usuario)){
				$errores[] = "El usuario no cumple los requisitos.";
				$todoBien = false;
			}
        }
        
		if(empty($_POST['email'])){
			$errores[] = "El email no puede estar vacio.";
			$todoBien = false;
		}else{
			$email = htmlspecialchars($_POST['email']);
			/*hay correo pero con error*/
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
				$errores[] = "El email no es valido.";
				$todoBien = false;		
			}
		}

		/*comprobar la clave*/
		if(empty($_POST['clave'])){
			$errores[] = "La clave no puede estar vacia.";
			$todoBien = false;
		}else{
			$clave = htmlspecialchars($_POST['clave']);
			/*hay correo pero con error*/
			if (!comprobarClave($clave)){
				$errores[] = "La clave no cumple los requisitos.";
				$todoBien = false;	
			}
		}

		/*comprobar la repetición de la clave*/
		if(empty($_POST['confirmarClave'])){
			$errores[] = "La confirmacion de la clave no puede estar vacia.";
			$todoBien = false;
		}else{
			$clave2 = htmlspecialchars($_POST['confirmarClave']);
			/*no comprobamos la clave, solo si es igual a la primera*/
			if (strcmp($clave1, $clave2)==0){
				$errores[] = "La clave no coincide con la confirmacion.";
				$todoBien = false;
			}
		}

		if(existeUsuario($usuario,$email)){
			$errores[] = "El nombre de usuario o el email ya esta en uso.";
			$todoBien = false;
		}
		
		if($todoBien){
			$clave = password_hash($clave, PASSWORD_DEFAULT);
			//Insertamos al usuario en la base de datos sin confirmar.
			insertarUsuario($usuario,$clave,$email);
			//Escribimos el cuerpo del correo de confirmacion.
			$cuerpo = "Activa tu cuenta pinchando en este link:<br><br><a href=\"http://localhost/aplicacion3/confirmar_usuario.php?usuario=".$usuario."\">CLICK AQUI</a>";
			correo_confirmacion($email,$cuerpo);//Se envia el correo.
			echo "TRUE";
		}else{
			echo json_encode($errores);
		}
    }