<?php

namespace Classes;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

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
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
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
        //Activar para produccion...
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->CharSet = 'UTF-8';

        $mail->Host = $this->host;
        $mail->SMTPAuth = true;
        $mail->Port = $this->port;
        $mail->Username = $this->username;
        $mail->Password = $this->password;

        $mail->setFrom('admin@cyberart.store');
        $mail->addAddress('admin@cyberart.store' , 'Administrador CyberArt');

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
        /* Activa solo para producction */
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->CharSet = 'UTF-8';

        $mail->Host = $this->host;
        $mail->SMTPAuth = true;
        $mail->Port = $this->port;
        $mail->Username = $this->username;
        $mail->Password = $this->password;

        $mail->setFrom('admin@cyberart.store');
        $mail->addAddress($this->email , $this->nombre);
        $mail->addReplyTo('admin@cyberart.store', 'Nueva orden');
        
        
        $mail->Subject = 'Pedido creado con Exito!';
        
        $productos_text = '';
        foreach($productos as $producto){
            $productos_text .= "<li><strong>$producto->nombre</strong> x <strong>$producto->cantidad</strong> </li>";
        };
        //formateo el precio
        $total = number_format($total, 0, ',', '.');        
        $contenido = <<<EOD
        <html>
        <p>Hola <strong> $this->nombre </strong> Has creado con exito tu pedido en CyberArt </p>
        <p></p>
        <h1>Gracias por tu compra!</h1>
        <div>
        <h2>Instrucciones a seguir</h2>
        <div>
            <p>Consigna a nuestra cuenta bancolombia #: <strong>076-379546-57</strong></p>
            <p>Manda un pantallazo a nuestro <strong> WhatsApp +57 321 3458210</strong> con el pedido # <strong>$pedidosId</strong></p>
            <p>Recibe tus productos en la puerta de tu casa...!</p>
        </div>
        <div></div>
        <h2>Resumen de tu pedido</h2>
        <p>El pedido quedo a nombre de : <strong> $nombre </strong>  </p>
        <p>El costo total a pagar es de : <strong> $$total </strong></p>
        <ul class="lista-productos" id="lista-productos">
            <p>Los productos que te mandaremos son:</p>
            $productos_text
        </ul>
        <p>La direccion a la que te mandaremos los productos es: <strong>$direccion</strong> </p>
        </div>
        <img src="https://cyberart.store/img/bancolombia-chico.png" alt="datos transferencia">
        <p> si tu no hiciste este pedido, puedes ignorar el mensaje </p>
        </html>
        
        EOD;
        // <img src="cid:myimage">
        $mail->Body = $contenido;
        
        $resultado = $mail->send();
        
        if(!$resultado){
            $resultado = $mail->ErrorInfo;
        }
        return $resultado;
        
    }
    public function enviar_formulario_landingPage($args){
        $mail = new PHPMailer();
        try {
            $mail->isSMTP();
        $mail->isHTML(TRUE);

        /* Activa solo para Produccion */
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        //Activar solo para Prueba
        /* $mail->SMTPDebug = SMTP::DEBUG_SERVER;  */
        $mail->CharSet = 'UTF-8';

        $mail->Host = $this->host;
        $mail->SMTPAuth = true;
        $mail->Port = $this->port;
        $mail->Username = $this->username;
        $mail->Password = $this->password;

        $mail->setFrom('admin@cyberart.store');
        $mail->addAddress('admin@cyberart.store' , 'Administrador CyberArt');        
        
        $mail->Subject = 'Nuevo pedido ';
        
        $propiedad_text = '';
        $oferta = [
            '1'=>'1x $79.900',
            '2'=> '2x $140.000'
        ];
         
        foreach($args as $key=>$value){
            if($key === 'metodoPago'){
                $key = 'Metodo de pago';
            }
            
            switch($key){
                case 'metodoPago':
                    $key = 'Metodo de pago';
                    
                    break;
                case 'oferta':
                    $value = $oferta[$value];
                    break;
                case 'otro_cuadro':
                    continue 2;
                case 'otro_producto_nombre':
                    if($value){
                        $key = 'Nombre del segundo cuadro';
                    }else{
                        /* $key = ''; */
                        continue 2;
                    }
                    break;
                default:
                    break;

            }
            
            $propiedad_text .= "<li>$key: <strong>$value</strong></li>";
        };  
        
        $contenido = <<<EOD
        <html>
        <p>Hola administrador de CyberArt, tienes un nuevo pedido</p>
        <ul class="lista-productos" id="lista-productos">
            <p>Los datos del cliente son: </p>
            $propiedad_text
        </ul>
        </html>
        EOD;
        // <img src="cid:myimage">
        $mail->Body = $contenido;
        
        $resultado = $mail->send();
        
        if(!$resultado){
            $resultado = $mail->ErrorInfo;
        }
        return $resultado;
          
        } catch (Exception $e) {
            return ['error'=>"Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
        }
    }
}