<?php
require "bd.php";
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
        $bd = new PDO($res[0], $res[1], $res[2]);
        //Consulta para cambiar el estado de confirmado en la tabla usuarios.
        $stmt = $bd->prepare("UPDATE usuarios SET confirmado = true WHERE usuario = :usuario");
        $stmt->bindValue(':usuario', $_POST['usuario']);

        if($stmt->execute()){//Si es true
            mkdir("usuarios/". $_POST['usuario']);//Se crea una carpeta dento del directorio usuarios con el nombre del usuario.
            header("Location: pagina_principal.php");//Nos redirige a la pagina principal.
        }else{
            echo "ERROR";
        }
    }   
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Confirmar Usuario</title>
        <meta charset="UTF-8">
        <script type="text/javascript" src="js/cargarDatos.js"></script>
        <script type="text/javascript" src="js/sesion.js"></script>
    </head>
    <body>
            <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method = "POST">
                <input type="hidden" id="usuario" name="usuario" value="<?php echo $_GET['usuario']; ?>">
                <input type="submit" value="Confirmar">
            </form>