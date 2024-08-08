<!-- <h1 class="nombre-pagina">Desde carrito_de_compras</h1> -->

<nav class="tabs">
    <button type="button" data-paso="1">Productos seleccionados</button>
    <button type="button" data-paso="2">Informacion para el pedido</button>
    <button type="button" data-paso="3">Compra</button>
</nav>
<?php /* include_once __DIR__ . '/../templates/alertas.php' */ ?>
<div id="alertas" class="contenedor_alertas"></div>
<section id="paso-1" class="contenedor section resumen_carrito_compras ocultar">
    <table class="tablaResumen tabla">
        <thead class="tabla__head">
            <tr class="tabla__head__row">
                <th class="tabla__head__row__th">Producto</th>
                <th class="tabla__head__row__th">Precio/Cantidad</th>
                <!-- <td class="tabla__head__row__td">Precio</td>
                <td class="tabla__head__row__td">total</td> -->
                
            </tr>
        </thead>
        <tbody id="tbody" class="tabla__body">
        
        </tbody >
    </table>
    <div class="contenedorProductosFormulario"></div>
</section>

<!-- Empieza paso 2 -->
<section id="paso-2" class="section formulario_carrito_de_compras ocultar">  
    <h2 class="formulario_carrito_de_compras__texto">Formulario de Compra</h2>
    <form method="post" class="formulario formulario--71rem" id="formulario_carrito_de_compras">
        <div class="formulario__campo">
            <div>
                <label class="formulario__label" for="nombre">Nombre</label>
                <input class="formulario__input" type="text" id="nombre" placeholder="Tu nombre" name="nombre">
            </div>
            <div>
                <label class="formulario__label" for="apellidos">Apellidos</label>
                <input class="formulario__input" type="text" id="apellidos" placeholder="Tus apellidos" name="apellido">
            </div>
        </div>
        
            <div class="formulario__campo formulario__campo--column formulario__campo--gap">
                <label for="direccion" class="formulario__label">Direccion</label>
                <input class="formulario__input" type="text" id="direccion" placeholder="Tu direccion" name="direccion">
            </div>
            <div class="formulario__campo">
                <select id="departamento"  name="departamento" class="formulario__select">
                    <option class="option" disabled selected >--Selecciona tu departamento--</option>
                </select>
                <select name="ciudad" disabled id="ciudad" class="formulario__select">
                    <option class="option" disabled selected >--Selecciona tu ciudad--</option>
                </select>
            </div>
           <!--  <input class="formulario__input" type="text" id="ciudad" placeholder="Tu ciudad" name="user[ciudad]"> -->
        
        <div class="formulario__campo formulario__campo--column">
            <label for="telefono" class="formulario__label">Telefono</label>
            <input class="formulario__input" type="tel" id="telefono" placeholder="Tu telefono" name="telefono">
        </div>
        <div class="formulario__campo formulario__campo--column">
            <label for="email" class="formulario__label">Email</label>
            <input class="formulario__input" type="email" id="email" placeholder="Tu email" name="email">
        </div>
        <div class="formulario__campo formulario__campo--column">
            <!-- <label class="label label_enviar_direccion_diferente">Enviar a direccion diferente <input class="formulario__input" id="byEmail" type="radio" name="user[direccion_diferente_de_envio]" value="true"></label> -->
             <label for="cedula" class="formulario__label">Documento de identificacion</label>
             <input class="formulario__input" name="cedula" id="cedula" type="numbers" placeholder="Escribe tu Documento de identificacion">
        </div>
        <div class="direccion_diferente_div">  </div>
        <div class="formulario__campo formulario__campo--column">
            <label for="informacionExtra" class="formulario__label">Informacion adicional</label>
            <textarea class="formulario__textarea" name="informacion_extra" id="informacionExtra" placeholder="Escribe cualquier informacion adicional que consideres importante" rows="2" cols="5"></textarea>
        </div>
       <!--  
        
        <input type="submit" class="boton" value="Validar datos"> -->
    </form>
</section>
<!-- Termina paso 2 -->

<!-- Comienza paso 3 metodo de pago -->

<section id="paso-3" class="contenedor section metodo_de_pago ocultar">

    <h2>Metodo de pago</h1>

    <div class="card">
        <div class="contenedor_pago_transferencia" id="contenedor_pago_transferencia" data-metodo-pago="transferencia">
            <div class="contenedor_metodo_pago">
                <input class="input_radio_metodo_pago" type="radio" id="input_pago_transferencia" name="metodo_pago_seleccionado" value="transferencia_bancolombia"  data-metodo-pago="transferencia">
                <label for="input_pago_transferencia">Transferencia Bancolombia</label>
            </div>
        </div>
        <!-- <div class="contenedor_crear_pedido">
            
        </div> -->
        <div class="contenedor_pago_PSE" id="contenedor_pago_PSE" data-metodo-pago="pse">
            <div class="contenedor_metodo_pago">
                <input class="input_radio_metodo_pago" type="radio" id="input_pago_PSE" name="metodo_pago_seleccionado" value="pse" data-metodo-pago="pse">
                <label for="input_pago_PSE">Transferencia PSE</label>
            </div>
        </div>
    </div>
</section>



<div class="paginacion">
        <button class="boton" id="anterior">&laquo; Anterior </button>
        <button class="boton" id="siguiente"> siguiente &raquo;</button>
        <!-- <input type="hidden" id="id" value="<?php /* echo $id */ ?>"> -->
</div>

<?php echo $script ?? '' ?>