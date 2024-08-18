<h1 class="nombre-pagina">Catalogo</h1>
<div class="contenedor contenedor_productos">
    <!-- foreach -->

    <?php foreach($productos as $producto): ?>
        <?php $producto->precio= number_format($producto->precio, 0, ',', '.'); ?>
        <div class="producto_div" data-id="<?php echo $producto->id ?>">
                <div class="producto_imagen">
                <img src="/img/productos/<?php echo $producto->imagen ?>" alt="imagen-producto">
            </div>
            <div class="info">
                <div class="producto_informacion">
                    <p class="nombre"><?php echo $producto->nombre ?></p>
                    <p class="precio">$<?php echo $producto->precio ?></p>
                    <!-- <p class="descripcion"><span>Descripcion</span><br><?php echo $producto->descripcion ?></p> -->
                    <p class="size"><span>Tamaño:</span> <?php echo $producto->size ?></p>
                    <p class="disponible"><?php echo $disponible[$producto->disponible] ?></p>
                </div>
                <!-- <div class="acciones">
                    <button data-id="<?php echo $producto->id ?>" class="boton-accion btn-agregar">Agregar al carrito</button>
                    <button data-id="<?php echo $producto->id ?>" class="boton-accion btn-comprar">Comprar ahora</button>
                </div> -->

            </div>
        </div>
    <?php endforeach ?>
</div>