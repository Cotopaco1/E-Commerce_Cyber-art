
<div class="card_login contenedor">
    <div class="datos_login">
        <div class="datos_header crear_cuenta_header">
            <div class="logo"><a href="/"><img src="./img/logo_cyber-transparente.png" alt="imagenLogo"></a></div>

            <p>¿Ya tienes una cuenta? <a href="/login"> Ingresa aca</a> </p>
        </div>
        <div class="datos_body">
            <div class="datos_body_titulo">
                <?php if(isset($alertas['error'])){ ?>
                    <h1> Token invalido</h1>
                    </div>
        </div>
        <div class="datos_footer">
            <div id="respuesta_servidor"></div>
        </div>
    </div>
    <div class="imagen_card_login"></div>
</div>
<?php echo $script ?? '' ?>
                    <?php } else{ ?>
                        <h1>Reestablece Tu Password</h1>
                        <p>Escribe tu nueva contraseña </p>
                        <div class="datos_body_inputs">
                            <form id="formulario" class="datos_body_inputs" >
                                <input autocomplete="new-password" name="password" type="password" class="input" id="password" placeholder="Ingresa tu nueva contraseña">
                                <input autocomplete="new-password" name="password_confirmar" type="password" class="input" id="password_confirmar" placeholder="Confirma tu contraseña">
                            </form>
                            
                        </div>
                        </div>
        </div>
        <div class="datos_footer">
            <boton class="botonNegro boton_login" id="recuperar_password">Recuperar contraseña</boton>
            <div id="respuesta_servidor"></div>
        </div>
    </div>
    <div class="imagen_card_login"></div>
</div>
<?php echo $script ?? '' ?>
                        <?php }  ?>