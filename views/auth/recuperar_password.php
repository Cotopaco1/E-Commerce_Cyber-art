
<div class="card_login contenedor">
    <div class="datos_login">
        <div class="datos_header crear_cuenta_header">
            <div class="logo"><a href="/"><img src="./img/logo_cyber-transparente.png" alt="imagenLogo"></a></div>

            <p>¿Ya tienes una cuenta? <a href="/login"> Ingresa aca</a> </p>
        </div>
        <div class="datos_body">
            <div class="datos_body_titulo">
                <h1>¿Olvidaste tu contraseña?</h1>
                <p>No te preocupes, te mandaremos las intrucciones para recuperarla a tu Email </p>
            </div>
            <div class="datos_body_inputs">
                <form id="formulario" class="datos_body_inputs" >
                    <input name="email" type="email" class="input" id="email" placeholder="Ingresa tu email">
                </form>
                
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