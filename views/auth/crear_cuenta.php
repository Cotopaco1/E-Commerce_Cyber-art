
<div class="card_login contenedor">
    <div class="datos_login">
        <div class="datos_header crear_cuenta_header">
            <h3>CyberArt</h3>
            <p>Ya tienes cuenta cuenta? <a href="http://localhost:3000/login"> Ingresa aca</a> </p>
        </div>
        <div class="datos_body">
            <div class="datos_body_titulo">
                <h1>Bienvenido, crea tu cuenta!</h1>
                <p>Bienvenido al panel de inicio a nuestro tienda </p>
            </div>
            <form class="datos_body_inputs" id="formulario">
                <input name="nombre" type="text" class="input" id="nombre" placeholder="Tu nombre">
                <input name="apellido" type="text" class="input" id="apellidos" placeholder="Tus apellidos">
                <input name="email" type="email" class="input" id="email" placeholder="Tu email">
                <input name="password" type="password" class="input" id="password" placeholder="Tu contraseña">
                <input name="password_confirmar" type="password" class="input" id="password_confirmar" placeholder="Confirma tu password">
                
                
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