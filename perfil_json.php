<?php
require "bd.php";
require_once "comprobar_sesion.php";

    comprobar_sesion();

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(existeUsuario($_POST['usuario'])){//Se comprueba que existe el usuario recibido por el metodo POST.
            $perfil = json_encode(cargar_perfil($_POST['usuario']));//Se carga su perfil codificado en JSON.
            echo $perfil;
        }else{//Si no existe el usuario:
            echo "FALSE";
        }
    }