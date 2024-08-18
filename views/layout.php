<?php
    $auth = $_SESSION['login'] ?? false ;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-S6YCG8CPV1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-S6YCG8CPV1');
    </script>
    <!-- Termina google Tag -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./build/css/app.css?v=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Termina google Fonts -->
    <!-- Open graph meta tags -->
    <meta property="og:title" content="Cyber-Art" >
    <meta property="og:description" content="Ordena y reciba los mejores cuadros decorativos" >
    <meta property="og:url" content="https://cyberart.store/ ">
    <meta property="og:image" content="https://cyberart.store/img/imagen_metaDatos.png" >
    <meta property="og:type" content="website">
    <!-- Termina meta datos -->
     
    <link rel="icon" href="/img/logo_cyberArt.ico" type="image/x-icon">
    <title>CyberArt</title>
</head>
<body>
    <a id="btn_compra_wp_flotante" class="contenedor_botonWp" href="https://api.whatsapp.com/send?phone=573213458210&text=Estoy viendo su pagina web, y quiero mas informacion">
        <i class="fa-brands fa-whatsapp"></i>
        
    </a>
    <header class="header">
    <div class="contenedor headerContenido">
            <div class="hamburgerMenu"><img src="menu.png" alt="hamburgerMenu"></div>
            <div class="logo">
                <a href="/">
                    <picture>
                        <source media="(min-width: 480px)" srcset="./img/logo_cyberArt-horizontal2.png">
                        <img src="./img/logo_cyberArt.png" alt="imagenLogo">
                    </picture>
                </a>
            </div>
            <div class="buttonsHeader">
                <!-- <img class="carritoCompra" src="img/iconos/carrito-compras.png" alt="imagenCarrito">
                <img class="userBoton" src="img/iconos/usuario.png" alt="imagenUsuario"> -->
                <i class="fa-solid fa-cart-shopping carritoCompra header__icono_carrito" id="icono_carrito"></i>
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
