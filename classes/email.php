<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token = NULL)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token ;
    }

    public function enviarEmail($tipo){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->isHTML(TRUE);
        $mail->SMTPSecure = 'tls';
        $mail->CharSet = 'UTF-8';

        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = '2525';
        $mail->Username = '6897693e24be95';
        $mail->Password = 'd8aafe3f04b8a5';

        $mail->setFrom('cyberart@gmail.com');
        $mail->addAddress($this->email , $this->nombre);
        
        if($tipo === 'confirmacion'){
            $mail->Subject = 'Confirma tu cuenta';
            $contenido = "<html>";
            $contenido .= "<p> <strong> Hola " . $this->nombre . "</strong> has creado tu cuenta en CyberArt, solo debes confirmarla presionando en el siguiente enlace: </p>";
            $contenido .= "<a href='http://localhost:3000/confirmar_cuenta?token=". $this->token ."'> Confirmar cuenta </a> ";
            $contenido .= "<p> si tu no solicitaste esta cuenta, puedes ignorar el mensaje </p>";
            $contenido .= "</html>";
    
            $mail->Body = $contenido;
            $mail->send();
            return true;
        }
        else if($tipo === 'olvide'){
            $mail->Subject = 'Recupera tu cuenta...';
            $contenido = "<html>";
            $contenido .= "<p> <strong> Hola " . $this->nombre . "</strong> has solicitado recuperar tu cuenta, crea una nueva password presionando en el siguiente enlace: </p>";
            $contenido .= "<a href='http://localhost:3000/recuperar?token=". $this->token ."'> Reestablece tu password </a> ";
            $contenido .= "<p> si tu no solicitaste esta cuenta, puedes ignorar el mensaje </p>";
            $contenido .= "</html>";

            $mail->Body = $contenido;
            $mail->send();
        }
        return 'El tipo de email no fue encontrado';

    }
    

}