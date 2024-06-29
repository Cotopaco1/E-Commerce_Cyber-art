<!-- <div id="carritoDeCompras">
    <div class="contenedorCarritoCompras"></div>
</div> -->
<h1 class="nombre-pagina">Cuadros personalizados</h1>

<section class="contenedor cuadros section" id="cuadros">
    
</section>



<section class="contenedorContacto section">
    <h2>Volvemos tu idea/Recuerdo en un cuadro personalizado</h2>
    <div class="contenedor contacto">
    <div class="imagenContacto">
        <img src="luffy.png" alt="imagenContacto">
    </div>
    <form action="/" class="formulario">
        <fieldset class="informacionPersonal">
            <legend>Informacion personal</legend>
            <div class="contenedorNombre">
                <div>
                    <label for="nombre">Nombre</label>
                    <input id="nombre" type="text" placeholder="Tu nombre" name="user[nombre]">
                </div>
                <div>
                    <label for="apellido">apellido</label>
                    <input id="apellido" type="text" placeholder="Tu apellido" name="user[apellido]">

                </div>
            </div>
        </fieldset>
        <fieldset class="contactoField">
            <legend>Contacto</legend>
            <p>Como desea ser contactado?</p>
            <div class="contactoRadio field">
                <label for="byEmail">Correo Electronico</label>
                <input id="byEmail" type="radio" name="user[contacto]" value="email">
                <label for="byTelefono">Telefono</label>
                <input id="byTelefono"  name="user[contacto]" type="radio" value="telefono">
            </div>
            <div class="contactoDiv field">  </div>
             
            <div class="field fieldMensaje">
                <label for="mensaje">Tu mensaje</label>
                <textarea name="user[mensaje]" id="mensaje"></textarea>
            </div>
            <input type="submit" class="boton">

        </fieldset>
    </form>
</section>
<section class="contenedor caracteristicas section">
    <div class="caracteristica">
        <img src="colombia.png" alt="imagenColombia">
        <p>Envios a todo colombia</p>
    </div>
    <div class="caracteristica">
        <img src="banderaColombia.png" alt="imagenBanderaColombia">
        <p>Emprendimiento Colombiano</p>
    </div>
    <div class="caracteristica">
        <img src="mercadoPago.png" alt="imagenColombia">
        <p>Pagos seguros por mercadopago</p>
    </div>

    </div>
</section>