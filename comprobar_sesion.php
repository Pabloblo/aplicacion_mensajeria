<?php
//Comprueba si la sesión está iniciada
    function comprobar_sesion(){
        session_start();
        if(!isset($_SESSION['usu'])){
            header("Location:pagina_principal.php");
        }
    }