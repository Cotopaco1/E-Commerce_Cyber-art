<?php
    $auth = $_SESSION['login'] ?? false ;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./build/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>CyberArt</title>
</head>
<body>
    <div class="modal ocultar">
        <div class="menu">
            <div class="menuHeader">
                <i class="fa-solid fa-x fa-2xl icono"></i>
                <div class="menuCampo">
                    <a href="/">Home</a>
                </div>
                <div class="menuCampo">
                    <a href="#">Lo mas vendido</a>
                </div>
                <div class="menuCampo">
                    <a href="#">Inspirate</a>
                </div>
                <div class="menuCampo">
                    <a href="#">Nuevas colecciones</a>
                </div>
                <div class="menuCampo">
                    <a href="#">Contactanos</a>
                </div>
                <div class="menuCampo">
                    <a href="#">Quienes somos?</a>
                </div>
            </div>
            <div class="menuFooter">
                <?php include __DIR__ . '/templates/redes.php' ?>
            </div>
        </div>
    </div>
    <header class="header">
        <div class="contenedor headerContenido">
            <div class="hamburgerMenu"><img src="menu.png" alt="hamburgerMenu"></div>
            <div class="logo"><img src="#" alt="imagenLogo"></div>
            <div class="buttonsHeader">
                <img class="carritoCompra" src="img/iconos/carrito-compras.png" alt="imagenCarrito">
                <img class="userBoton" src="img/iconos/usuario.png" alt="imagenUsuario">
                
                <?php if($auth ?? false): ?>
                <a href="/logout" class="link_cerrar_sesion">Cerrar sesion</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
<main>

    <?php echo $contenido ?>
</main>


<footer class="footer ">
    <?php include __DIR__ . '/templates/redes.php' ?>
    <div class="categorias contenedor">
        <a href="#"><p>Lo mas vendido</p></a>
        <a href="#"><p>Inspirate</p></a>
        <a href="#"><p>nuevas colecciones</p></a>
        <a href="#"><p>Quienes somos?</p></a>
    </div>
    <p>Copyright <?php echo date('Y'); ?>, designed by Sergio Silva</p>
</footer>
<script src="./build/js/app.js"></script>

</body>
</html>
