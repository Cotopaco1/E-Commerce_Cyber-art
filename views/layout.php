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
    <header class="header">
        <div class="contenedor headerContenido">
            <div class="hamburgerMenu"><img src="menu.png" alt="hamburgerMenu"></div>
            <div class="logo"><a href="/"><img src="./img/logo_cyber-transparente.png" alt="imagenLogo"></a></div>
            <div class="buttonsHeader">
                <!-- <img class="carritoCompra" src="img/iconos/carrito-compras.png" alt="imagenCarrito">
                <img class="userBoton" src="img/iconos/usuario.png" alt="imagenUsuario"> -->
                <i class="fa-solid fa-cart-shopping carritoCompra"></i>
                <!-- <i class="fa-regular fa-user userBoton"></i> -->
                
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
        <a href="/"><p>Home</p></a>
        <a href="/productos"><p>Todos los productos</p></a>
        <!-- <a href="/contacto"><p>Contactanos</p></a>
        <a href="/nosotros"><p>Nosotros</p></a> -->
    </div>
    <p>Copyright <?php echo date('Y'); ?>, designed by Sergio Silva</p>
</footer>
<script src="./build/js/app.js"></script>
<?php echo $script ?? '' ?>
</body>
</html>
