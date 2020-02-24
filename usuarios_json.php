<?php
    require "bd.php";
    require_once "comprobar_sesion.php";

    comprobar_sesion();
    
    $usuarios = [];
    //Se almacenan en un array JSON todos los usuarios de la aplicacion obtenidos mediante la funcion "cargar_usuarios()".
    $usuarios = json_encode(cargar_usuarios());
    echo $usuarios;