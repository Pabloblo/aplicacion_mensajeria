<?php
require "bd.php";
require_once "comprobar_sesion.php";

    comprobar_sesion();

    $mensajes = [];
    $mensajes = json_encode(cargar_mensajes($_SESSION['usu']['usuario']));
    echo $mensajes;