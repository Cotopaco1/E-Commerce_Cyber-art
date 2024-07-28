<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{

    public $email;
    public $nombre;
    public $token;
    public $mensaje;
    protected $host;
    protected $port;
    protected $username;
    protected $password;

    public function __construct($email, $nombre, $token = NULL,$mensaje = '')
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token ;
        $this->mensaje = $mensaje;
        $this->host = $_ENV['EMAIL_HOST'];
        $this->port = $_ENV['EMAIL_PORT'];
        $this->username = $_ENV['EMAIL_USERNAME'];
        $this->password = $_ENV['EMAIL_PASSWORD'];
    }

    public function enviarEmail($tipo){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->isHTML(TRUE);
        $mail->SMTPSecure = 'tls';
        $mail->CharSet = 'UTF-8';

        $mail->Host = $this->host;
        $mail->SMTPAuth = true;
        $mail->Port = $this->port;
        $mail->Username = $this->username;
        $mail->Password = $this->password;

        $mail->setFrom('cyberart@gmail.com');
        $mail->addAddress($this->email , $this->nombre);
        
        if($tipo === 'confirmacion'){
            $mail->Subject = 'Confirma tu cuenta';
            $contenido = "<html>";
            $contenido .= "<p> <strong> Hola " . $this->nombre . "</strong> has creado tu cuenta en CyberArt, solo debes confirmarla presionando en el siguiente enlace: </p>";
            $contenido .= "<a href='http://{$_ENV['DOMINIO_NAME']}/confirmar_cuenta?token=". $this->token ."'> Confirmar cuenta </a> ";
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
            $contenido .= "<a href='http://{$_ENV['DOMINIO_NAME']}/reestablecer_password?token=". $this->token ."'> Reestablece tu password </a> ";
            $contenido .= "<p> si tu no solicitaste esta cuenta, puedes ignorar el mensaje </p>";
            $contenido .= "</html>";

            $mail->Body = $contenido;
            $mail->send();
            return true;
        }
        return 'El tipo de email no fue encontrado';
    }
    public function enviar_email_cuadro_personalizado_al_administrador(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->isHTML(TRUE);
        $mail->SMTPSecure = 'tls';
        $mail->CharSet = 'UTF-8';

        $mail->Host = $this->host;
        $mail->SMTPAuth = true;
        $mail->Port = $this->port;
        $mail->Username = $this->username;
        $mail->Password = $this->password;

        $mail->setFrom('cyberart@pagina.com');
        $mail->addAddress('cyberart@admin.com' , 'Administrador CyberArt');

        $mail->Subject = 'Nuevo cliente interesado';
            $contenido = "<html>";
            $contenido .= "<p> <strong> Hola Administrador de CyberArt</strong> Tienes un nuevo cliente interesado en un cuadro personalizado, aca estan los datos: </p>";
            $contenido .= "<p>Nombre: <strong>$this->nombre</strong> </p>";
            $contenido .= "<p>contacto: <strong>$this->email</strong> </p>";
            $contenido .= "<p>Mensaje(opcional): <strong>$this->mensaje</strong> </p>";
            $contenido .= "</html>";
            
            $mail->Body = $contenido;
            $resultado = $mail->send();
            return $resultado;
    }
    public function enviar_email_nueva_orden($pedidosId,$nombre,$total,$direccion,$productos){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->isHTML(TRUE);
        $mail->SMTPSecure = 'tls';
        $mail->CharSet = 'UTF-8';

        $mail->Host = $this->host;
        $mail->SMTPAuth = true;
        $mail->Port = $this->port;
        $mail->Username = $this->username;
        $mail->Password = $this->password;

        $mail->setFrom('cyberart@gmail.com');
        $mail->addAddress($this->email , $this->nombre);
        
        
        $mail->Subject = 'Pedido creado con Exito!';
        
        $productos_text = '';
        foreach($productos as $producto){
            $productos_text .= "<li>$producto->nombre x $producto->cantidad </li>";
        };

        $contenido = <<<EOD
        <html>
        <p>Hola <strong> $this->nombre </strong> Has creado con exito tu pedido en CyberArt </p>
        <p></p>
        <h1>Gracias por tu compra!</h1>
        <div>
        <h2>Instrucciones a seguir</h2>
        <div>
            <p>Consigna a nuestra cuenta bancolombia #: <span>412514</span></p>
            <p>Manda un screenshot a nuestro wp +57 321 3458210 con el pedido # <span>$pedidosId</span></p>
            <p>Recibe tus productos en la puerta de tu casa...!</p>
        </div>
        <div></div>
        <h2>Resumen de tu pedido</h2>
        <p>El pedido quedo a nombre de : <span> $nombre </span>  </p>
        <p>El costo total a pagar es de : <span> $total </span></p>
        <ul class="lista-productos" id="lista-productos">
            <p>Los productos que te mandaremos son:</p>
            $productos_text
        </ul>
        <p>La direccion a la que te mandaremos los productos es: <span>$direccion</span> </p>
        </div>
        </html>
        
        EOD;
        $contenido .= "<p> si tu no hiciste este pedido, puedes ignorar el mensaje </p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;
        $resultado = $mail->send();
        return $resultado;
        
    }

}