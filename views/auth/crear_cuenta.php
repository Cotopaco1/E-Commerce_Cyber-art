
<div class="card_login contenedor">
    <div class="datos_login">
        <div class="datos_header crear_cuenta_header">
            <div class="logo"><a href="/"><img src="./img/logo_cyber-transparente.png" alt="imagenLogo"></a></div>

            <p>Ya tienes cuenta cuenta? <a href="/login"> Ingresa aca</a> </p>
        </div>
        <div class="datos_body">
            <div class="datos_body_titulo">
                <h1>Bienvenido, crea tu cuenta!</h1>
                <p>Escribe estos datos para obtener full acceso a nuestra tienda </p>
            </div>
            <form class="datos_body_inputs" id="formulario">
                <input autocomplete="username" name="nombre" type="text" class="input" id="nombre" placeholder="Tu nombre">
                <input autocomplete="family-name" name="apellido" type="text" class="input" id="apellido" placeholder="Tus apellidos">
                <input autocomplete="email" name="email" type="email" class="input" id="email" placeholder="Tu email">
                <input autocomplete="new-password" name="password" type="password" class="input" id="password" placeholder="Tu contraseña">
                <input autocomplete="new-password" name="password_confirmar" type="password" class="input" id="password_confirmar" placeholder="Confirma tu password">
                
                
            </form>
        </div>
        <div class="mensaje" id="mensaje"></div>
        <div class="datos_footer">
            <boton class="botonNegro boton_login" id="boton_crear_cuenta">Crear cuenta</boton>
            
        </div>
    </div>
    <div class="imagen_card_login"></div>
</div>
<?php echo $script ?? '' ?>