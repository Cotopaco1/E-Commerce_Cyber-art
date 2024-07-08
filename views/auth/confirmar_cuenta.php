
<div class="card_login contenedor">
    <div class="datos_login">
        <div class="datos_header crear_cuenta_header">
            <h3>CyberArt</h3>
            <p>¿Ya tienes una cuenta? <a href="http://localhost:3000/login"> Ingresa aca</a> </p>
        </div>
        <div class="datos_body datos_body_confirmar_cuenta">
            <div class="datos_body_titulo">
                <?php 
                $confirmado = false;
                foreach($alertas as $tipoAlerta=> $value):
                    $confirmado = $tipoAlerta;
                    foreach($value as $mensaje): ?>
                    
                    <h1 class="alerta <?php echo $tipoAlerta ?> alerta_confirmar_cuenta"><?php echo $mensaje?></h1>
                    <?php endforeach; 
                endforeach;
                if($confirmado === 'error'){?>
                        <p>No tienes una cuenta? <a href="/crear_cuenta">Presiona aca para crearla</a></p>
                    </div>
                    <div class="datos_body_inputs">
                        <p>Quieres que te reenviemos el correo para confirmar tu cuenta?
                        <a href="/reenviar_email" class="">Presiona aqui</a> </p>
                        
                    </div>
                    </div>
                    <!-- <div class="datos_footer">
                        <a href="/login" class="botonAzul">Presioname para disfrutar tu nueva cuenta</a>
                                
                    </div> -->
                    </div>
                        <div class="imagen_card_login"></div>
                    </div>
                   <?php return;
                }  
                ?>
                <p>Tu cuenta ha sido confirmada</p>
                <p>Ahora ya puedes realizar todas las acciones en nuestra pagina web 😎👍</p>
            </div>
            <div class="datos_body_inputs">
                <a href="/login" class="botonNegro boton_disfrutar_cuenta">Presioname para disfrutar tu nueva cuenta</a>
            </div>
        </div>
        <div class="datos_footer">
            
        </div>
    </div>
    <div class="imagen_card_login"></div>
</div>