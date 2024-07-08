
<div class="card_login contenedor">
    <div class="datos_login">
        <div class="datos_header crear_cuenta_header">
            <h3>CyberArt</h3>
            <p>Ya tienes cuenta cuenta? <a href="http://localhost:3000/login"> Ingresa aca</a> </p>
        </div>
        <div class="datos_body">
            <div class="datos_body_titulo">
                <h1>Deseas que te reenviemos el correo de confirmacion?</h1>
                <p>Escribe tu correo asociado no confirmado a continuacion</p>
            </div>
            <form class="datos_body_inputs" id="formulario">
                <input autocomplete="email" name="email" type="email" class="input" id="email" placeholder="Tu email">
            </form>
        </div>
        <div class="mensaje" id="mensaje"></div>
        <div class="datos_footer">
            <boton class="botonNegro boton_login" id="boton_reenviar">Reenviar correo de confirmacion</boton>
            <div class="respuesta_servidor" id="respuesta_servidor"></div>
        </div>
    </div>
    <div class="imagen_card_login"></div>
</div>
<?php echo $script ?? '' ?>