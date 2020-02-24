<?php
require "bd.php";
    //Funcion que devuelve un array con todos los datos del usuario que intenta logearse si este introduce la clave correcta y esta confirmado en la base de datos.
    function comprobar_usuario_rol($usuario, $clave){
        $res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
        $bd = new PDO($res[0], $res[1], $res[2]);

        $stmt = $bd->prepare("SELECT idUsu, usuario, email, clave, rol, confirmado FROM usuarios WHERE usuario = :usuario or email = :usuario");
        $stmt->bindParam(':usuario', $usuario);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (password_verify($clave, $result['clave']) && $result['confirmado'] == true) ? $result : false;
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        //Se llama la la funcion para comprobar usuario y se guarda el resultado en una variable.
        $usu = comprobar_usuario_rol($_POST['usuario'], $_POST['clave']);
        if($usu===FALSE){
            echo "FALSE";
        }else{
            session_start();
            $_SESSION['usu'] = $usu;
            echo $usu['rol'];
        }
    }