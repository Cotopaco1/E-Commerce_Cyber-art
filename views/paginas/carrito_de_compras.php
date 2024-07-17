<h1 class="nombre-pagina">Desde carrito_de_compras</h1>

<nav class="tabs">
    <button type="button" data-paso="1">Productos seleccionados</button>
    <button type="button" data-paso="2">Informacion para el pedido</button>
    <button type="button" data-paso="3">Compra</button>
</nav>
<?php /* include_once __DIR__ . '/../templates/alertas.php' */ ?>
<div id="alertas"></div>
<section id="paso-1" class="contenedor section resumen_carrito_compras ocultar">
<table class="tablaResumen">
    <thead>
        <tr>
            <td>Detalles del producto</td>
            <td>Cantidad</td>
            <td>Precio</td>
            <td>total</td>
        </tr>
    </thead>
    <tbody id="tbody">
       
    </tbody>
</table>
<div class="contenedorProductosFormulario"></div>
</section>

<!-- Empieza paso 2 -->
<section id="paso-2" class="contenedor section formulario_carrito_de_compras ocultar">  
    <form method="post" class="form" id="formulario_carrito_de_compras">
        <h2>Rellena los datos...</h2>
        <div class="campoFormulario campo_nombre_and_apellido">
            <label class="label" for="nombre">Nombre</label>
            <input type="text" id="nombre" placeholder="Tu nombre" name="nombre">
            <label class="label" for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" placeholder="Tus apellidos" name="apellido">
        </div>
        <div class="campoFormulario">
            <label for="direccion" class="label">Direccion</label>
            <input type="text" id="direccion" placeholder="Tu direccion" name="direccion">
            <select id="departamento"  name="departamento" class="select">
                <option class="option" disabled selected >--Selecciona tu departamento--</option>
            </select>
            <select name="ciudad" disabled id="ciudad" class="select">
                <option class="option" disabled selected >--Selecciona tu ciudad--</option>
            </select>
           <!--  <input type="text" id="ciudad" placeholder="Tu ciudad" name="user[ciudad]"> -->
        </div>
        <div class="campoFormulario">
            <label for="telefono" class="label">Telefono</label>
            <input type="tel" id="telefono" placeholder="Tu telefono" name="telefono">
        </div>
        <div class="campoFormulario">
            <label for="email" class="label">Email</label>
            <input type="email" id="email" placeholder="Tu email" name="email">
        </div>
        <div class="campoFormulario">
            <!-- <label class="label label_enviar_direccion_diferente">Enviar a direccion diferente <input id="byEmail" type="radio" name="user[direccion_diferente_de_envio]" value="true"></label> -->
             <label for="cedula" class="label">Documento de identificacion</label>
             <input name="cedula" id="cedula" type="numbers" placeholder="Escribe tu Documento de identificacion">
        </div>
        <div class="direccion_diferente_div">  </div>
        <div class="campoFormulario">
            <label for="informacionExtra" class="label">Informacion adicional</label>
            <textarea class="textArea" name="informacion_extra" id="informacionExtra" placeholder="Escribe cualquier informacion adicional que consideres importante" rows="2" cols="5"></textarea>
        </div>
        
        
        <input type="submit" class="boton" value="Validar datos">
    </form>
</section>
<!-- Termina paso 2 -->

<!-- Comienza paso 3 metodo de pago -->

<section id="paso-3" class="contenedor section metodo_de_pago ocultar">

    <h2>Metodo de pago</h1>

    <div class="card">
        <div class="contenedor_metodo_pago contenedor_pago_transferencia" id="contenedor_pago_transferencia" data-metodo-pago="transferencia">
            <input class="input_radio_metodo_pago" type="radio" id="input_pago_transferencia" name="metodo_pago_seleccionado" value="transferencia_bancolombia"  data-metodo-pago="transferencia">
            <label for="input_pago_transferencia">Transferencia Bancolombia</label>
        </div>
        <!-- <div class="contenedor_crear_pedido">
            
        </div> -->
        <div class="contenedor_metodo_pago contenedor_pago_PSE" id="contenedor_pago_PSE" data-metodo-pago="pse">
            <input class="input_radio_metodo_pago" type="radio" id="input_pago_PSE" name="metodo_pago_seleccionado" value="pse" data-metodo-pago="pse">
            <label for="input_pago_PSE">Transferencia PSE</label>
        </div>
    </div>
</section>



<div class="paginacion">
        <button class="boton" id="anterior">&laquo; Anterior </button>
        <button class="boton" id="siguiente"> siguiente &raquo;</button>
        <!-- <input type="hidden" id="id" value="<?php /* echo $id */ ?>"> -->
</div>

<?php echo $script ?? '' ?>