<h1 class="nombre-pagina">Desde carrito_de_compras</h1>
<section class="contenedor section formulario_carrito_de_compras">
    
    <form method="post" class="form">
        <h2>Rellena los datos...</h2>
        <div class="campoFormulario campo_nombre_and_apellido">
            <label class="label" for="nombre">Nombre</label>
            <input type="text" id="nombre" placeholder="Tu nombre" name="user[nombre]">
            <label class="label" for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" placeholder="Tus apellidos" name="user[apellido]">
        </div>
        <div class="campoFormulario">
            <label for="direccion" class="label">Direccion</label>
            <input type="text" id="direccion" placeholder="Tu direccion" name="user[direccion]">
            <select id="departamento"  name="user[departamento]" class="select">
                <option class="option" disabled selected>--Selecciona tu departamento--</option>
            </select>
            <select name="user[ciudad]" disabled id="ciudad" class="select">
                <option class="option" disabled selected>--Selecciona tu ciudad--</option>
            </select>
           <!--  <input type="text" id="ciudad" placeholder="Tu ciudad" name="user[ciudad]"> -->
        </div>
        <div class="campoFormulario">
            <label for="telefono" class="label">Telefono</label>
            <input type="tel" id="telefono" placeholder="Tu telefono" name="user[telefono]">
        </div>
        <div class="campoFormulario">
            <label for="email" class="label">Email</label>
            <input type="email" id="email" placeholder="Tu email" name="user[email]">
        </div>
        <div class="campoFormulario">
            <label class="label label_enviar_direccion_diferente">Enviar a direccion diferente <input id="byEmail" type="radio" name="user[direccion_diferente_de_envio]" value="true"></label>
        </div>
        <div class="campoFormulario">
            <textarea class="textArea" name="user[informacion_extra]" id="informacionExtra" placeholder="Escribe cualquier informacion adicional que consideres importante" rows="2" cols="5"></textarea>
        </div>
        <div class="contactoDiv field">  </div>
        <input type="submit" class="boton" value="Enviar">
    </form>
    
</section>