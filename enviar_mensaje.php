<?php
require "bd.php";
require_once "comprobar_sesion.php";

comprobar_sesion();
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(insertarMensaje($_SESSION['usu']['usuario'],$_POST['destinatarios'],$_POST['asunto'],$_POST['cuerpo'])){
            echo "TRUE";
        }else{
            echo "FALSE";
        };
    }