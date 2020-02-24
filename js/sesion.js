function login(formu){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            if(this.responseText==="FALSE"){
                alert("Revise usuario y contraseña");
            }else{
                document.getElementById("principal").style.display = "block";
                document.getElementById("login").style.display = "none";
                document.getElementById("cab_principal").style.display ="none";

                document.getElementById("cab_usuario").innerHTML = "Usuario: "+ usuario;
                document.getElementById("perfil_usu").setAttribute("onclick", 'tablaPerfil(\''+ usuario +'\');');
                tablaMensajesRecibidos();

                if(this.responseText==="1"){
                    document.getElementById("admin").style.display = "inline";
                }else{
                    document.getElementById("admin").style.display = "none";
                }
            }
        }
    }
    var usuario = document.getElementById("usuario").value;
    var clave = document.getElementById("clave").value;
    var params = "usuario="+usuario+"&clave="+clave;
    xhttp.open("POST", "login.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}

function cerrarSesion(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            document.getElementById("principal").style.display = "none";
            document.getElementById("cab_principal").style.display ="inline";
            document.getElementById("login").style.display = "block";
            alert("Sesion cerrada con exito");
        }
    }
    xhttp.open("GET", "logout.php", true);
    xhttp.send();
    return false;
}

function registro(formu){
    var form = document.getElementById("formuRegistro");
    var params = new FormData(form);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            if(this.responseText!="TRUE"){
                var errores = JSON.parse(this.responseText);

                for (let index = 0; index < errores.length; index++) {
                    alert(errores[index]);
                }
            }else{
                alert("Comprueba tu correo electronico! Te hemos mandado un email para confirmar tu cuenta.");
            }
        }
    }
    xhttp.open("POST","registro.php",true);
    xhttp.send(params);
    return false;
}

function enviarMensaje(formu){
    var form = document.getElementById("formuMensaje");
    var params = new FormData(form);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            if(this.responseText==="FALSE"){
                alert("Has introducido algun dato incorrecto");
            }else{
                alert("Mensaje enviado!");
                formularioMensaje();
            }
        }
    }
    xhttp.open("POST","enviar_mensaje.php",true);
    xhttp.send(params);
    return false;
}

//Funcion que recoge los valores del formulario para editar el perfil y los envia a "actualizar_perfil.php"
function actualizarPerfil(){
    var form = document.getElementById("formuPerfil");
    var usuario = document.getElementById("usuario").value;
    var params = new FormData(form);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            if(this.responseText==="TRUE"){
                alert("Perfil actualizado!");
            }else{
                var errores = JSON.parse(this.responseText);
                for (let index = 0; index < errores.length; index++) {
                    alert(errores[index]);
                }
            }
            tablaPerfil(usuario);
        }
    }
    xhttp.open("POST","actualizar_perfil.php",true);
    xhttp.send(params);
    return false;
}