<?php 	
 	use PHPMailer\PHPMailer\PHPMailer;
 	require "vendor/autoload.php";
    
    function correo_confirmacion($destinatario,  $cuerpo,  $asunto = "Activa tu cuenta"){
        $mail = new PHPMailer();		
        $mail->IsSMTP(); 					
        $mail->SMTPDebug  = 0;  // cambiar a 1 o 2 para ver errores
        $mail->SMTPAuth   = true;                  
        $mail->SMTPSecure = "tls";                 
        $mail->Host       = "smtp.gmail.com";      
        $mail->Port       = 587;                   
        $mail->Username   = "noreplyaplicacion@gmail.com";  //usuario de gmail
        $mail->Password   = "C-nfirmaci-n123"; //contraseña de gmail          
        $mail->SetFrom('noreplyaplicacion@gmail.com', 'Confirmacion de usuario');
        $mail->Subject    = $asunto;
        $mail->MsgHTML($cuerpo);
        $mail->AddAddress($destinatario, "Test");
        $mail->Send();
    }	