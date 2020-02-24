<?php
require "bd.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $errores = array();
        $todoBien = TRUE;
        
        if(isset($_FILES['avatar'])){//Si hay un archivo seleccionado
            //Comprobamos los posibles errores
            if($_FILES['avatar']['error'] == 0){//Si devuelve 0 no hay error.
                $path = $_FILES['avatar']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if($ext == "png" or $ext == "jpg"){//Comprobamos la extension del archivo.
                    $nombreAvatar = $_POST['usuario']."-avatar";//Nombre del archivo
                    $dir = $_SERVER['DOCUMENT_ROOT']. '/aplicacion_mensajeria/usuarios/Avatares/';//Ruta del archivo
                    move_uploaded_file($_FILES['avatar']['tmp_name'], $dir.$nombreAvatar);//Se mueve el archivo a la ruta con el nombre especificado.
                    cambiarAvatar($_POST['usuario'],$nombreAvatar);//Se cambia el avatar en la base de datos.
                }else{
                    $errores[] = "El archivo tiene que tener extension png o jpg";
                    $todoBien = FALSE;
                }
            }else{
                if($_FILES['avatar']['error'] == 1 or $_FILES['avatar']['error'] == 2){//Si devuelve 1 o 2
                    $errores[] = "El archivo es demasiado grande.";
                }else{
                    $errores[] = "Error al procesar el archivo.";
                }
                $todoBien = FALSE;
            }
        }

        /*Codigo comentado porque da errores. Funciones cambiarDescripcion() y cambiarCiudad() en "bd.php".
            if(isset($_POST['descripcion'])){
            if(!cambiarDescripcion($_POST['usuario'],$_POST['descripcion'])){
                $errores[] = "Error al actualizar la descripcion";
                $todoBien = FALSE;
            }
        }

        if(isset($_POST['ciudad'])){
            if(!cambiarCiudad($_POST['usuario'],$_POST['ciudad'])){
                $errores[] = "Error al actualizar la ciudad";
                $todoBien = FALSE;
            }
        }*/

        if($todoBien == TRUE){//Si todo va bien devuelve "TRUE"
            echo "TRUE";
        }else{//Si no, devuelve el arraycon los distintos errores.
            echo json_encode($errores);
        }
    }
