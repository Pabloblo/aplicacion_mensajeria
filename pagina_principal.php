<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <meta charset="UTF-8">
        <script type="text/javascript" src="js/cargarDatos.js"></script>
        <script type="text/javascript" src="js/sesion.js"></script>
    </head>
    <body>
        <header>
            <?php require 'cabecera_principal.php' ?>
        </header>
        <section id="login">
            <form onsubmit="return login()" method = "POST">
                Usuario <input id="usuario" type="text">
                Clave   <input id="clave" type="password">
                <input type="submit" value="Acceder">
            </form>
        </section>
        <section id="registro"></section>
        <section id="principal" style="display:none">
            <header>
                <?php require 'cabecera_usuario.php' ?>
            </header>
            <section id="enviar_mensaje"></section>
            <section id="bandeja_entrada"></section>
            <section id="vista_mensaje"></section>
            <section id="perfil"></section>
        </section>
        