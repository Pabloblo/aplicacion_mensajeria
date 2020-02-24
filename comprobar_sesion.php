<?php
    function comprobar_sesion(){
        session_start();
        if(!isset($_SESSION['usu'])){
            header("Location:pagina_principal.php");
        }
    }