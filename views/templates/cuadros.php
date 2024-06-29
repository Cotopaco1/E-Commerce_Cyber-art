<?php foreach($cuadros as $cuadro): ?>
        <div class="cuadro">
        <div class="imagenCuadroDiv" style="background-image: url(/img/<?php echo $cuadro->imagen ?>);">
                <div class="cuadro-info">
                    
                    <p class="nombre-cuadro"><?php echo $cuadro->nombre ?></p>
                    <p class="precio-cuadro"><?php echo number_format($cuadro->precio, 2, '.', ',') ?> </p>
                </div>
                
            </div>
        </div>
    <?php endforeach ?>