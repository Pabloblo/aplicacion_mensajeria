<?php
require "bd.php";
require_once "comprobar_sesion.php";

    comprobar_sesion();

    if($_SERVER["REQUEST_METHOD"] == "POST"){//Pasamos las variables $_POST['idMensaje'] y $_SESSION['usu']['usuario'] a la funcion cargar_datos_mensaje() y a setLeido().
        $mensaje = json_encode(cargar_datos_mensaje($_POST['idMensaje'], $_SESSION['usu']['usuario']));
        setLeido($_POST['idMensaje'], $_SESSION['usu']['usuario']);
        echo $mensaje;
    }