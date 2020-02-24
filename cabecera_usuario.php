<header>
    <span id="cab_usuario"></span>
    <a href="#" onclick="tablaMensajesRecibidos();">Bandeja de Entrada</a>
    <a href="#" onclick="formularioMensaje();">Enviar mensaje</a>
    <a id="perfil_usu" href="#">Perfil</a>
    <a href="#" onclick="cerrarSesion();">Cerrar sesion</a>
    <a id="admin" href="#" style="display:none">Zona admin</a>
    <form onsubmit="return buscarPerfil();" style="display: inline;">
        <input type="search" id="busqueda" name="busqueda">
        <input type="submit" value="Buscar">
    </form>
</header>
<hr>