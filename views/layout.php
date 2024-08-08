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
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Termina google Fonts -->
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
<main class="<?php echo $main_class ?? '' ?>" >

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
