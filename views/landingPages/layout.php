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
    <link rel="icon" href="/img/logo_cyber-16x16.ico" type="image/x-icon">
    <title>CyberArt</title>
</head>
<body class="body_lp">
    <div class="cuenta_regresiva">
        <h2 class="cuenta_regresiva__titulo">Oferta por tiempo Limitado!</h2>
        <div class="cuenta_regresiva__div">
            <!-- <p class="cuenta_regresiva__texto">🔥 Oferta termina en: </p> -->
            <p class="cuenta_regresiva__tiempo" ><span id="cuenta_regresiva__horas"></span>horas</p>
            <div class="cuenta_regresiva__separador">•</div>
            <p class="cuenta_regresiva__tiempo" ><span id="cuenta_regresiva__minutos"></span>minutos</p>
            <div class="cuenta_regresiva__separador">•</div>
            <p class="cuenta_regresiva__tiempo" ><span id="cuenta_regresiva__segundos"></span>segundos</p>
        </div>
    </div>
    <header class="header_lp">
        <a href="/">
        <img class="header_lp__logo" src="img/logo_cyberArt-horizontal2.png" alt="logo cyberart">
        </a>
    </header>
    
<?php echo $contenido; ?>
<footer class="footer_lp">
    <h2 class="footer_lp__titulo footer_lp__titulo--blanco">¿Tienes alguna duda?</h2>
    <h3 class="footer_lp__subtitulo">Revisa las preguntas frecuentes</h3>
    <div class="footer_lp__campo">
        <div class="flex">
            <img class="footer_lp__icono" src="img/iconos/arrow-down.svg" alt="">
            <p class="footer_lp__texto">¿Ofrecen pago a Contra Entrega?</p>
        </div>
        <div class="footer_lp__respuesta" >
            <p class="footer_lp__texto" >Si, ofrecemos pago contra entrega a nivel nacional, ordenas y pagas en la puerta de tu casa</p>
        </div>
    </div>
    <div class="footer_lp__campo">
        <div class="flex"> 
            <img class="footer_lp__icono" src="img/iconos/arrow-down.svg" alt="">
            <p class="footer_lp__texto">¿El envio es gratis?</p>
        </div>
        <div class="footer_lp__respuesta" >
            <p class="footer_lp__texto" >Si, el envio es gratuito a nivel nacional</p>
        </div>
    </div>
    <div class="footer_lp__campo">
        <div class="flex">
            <img class="footer_lp__icono" src="img/iconos/arrow-down.svg" alt="">
            <p class="footer_lp__texto">¿Que otros metodos de pago tienen?</p>
        </div>
        <div class="footer_lp__respuesta" >
            <p class="footer_lp__texto" >Tenemos transferencia bancaria y pago contra entrega</p>
        </div>
    </div>
    <div class="footer_lp__campo">
        <div class="flex">
            <img class="footer_lp__icono" src="img/iconos/arrow-down.svg" alt="">
            <p class="footer_lp__texto">¿Ofrecen garantia?</p>
        </div>
        <div class="footer_lp__respuesta" >
            <p class="footer_lp__texto" >Todos nuestros cuadros tienen una garantia de 6 meses, revisar <a class="color-azul-claro" href="#">terminos y condiciones aca</a></p>
        </div>
    </div>
    <div class="footer_lp__campo">
        <div class="flex">
            <img class="footer_lp__icono" src="img/iconos/arrow-down.svg" alt="">
            <p class="footer_lp__texto">¿Como proceder a Garantia?</p>
        </div>
        <div class="footer_lp__respuesta" >
            <p class="footer_lp__texto" >Escribenos a nuestro <span>WhatsApp +57 321 3458210</span> y uno de nuestros asesores te atendera en el menor tiempo posible</p>
        </div>
    </div>
    <?php
    include_once __DIR__ . '/../templates/redes.php';
    ?>
    <div class="footer_lp__contenedor--center">
        <a href="/"><img class="footer_lp__logo" src="img/logo_cyberArt-horizontal2.png" alt="logo cyberart"></a>
    </div>
    <div class="footer_lp__copyright">
        <p class="footer_lp__texto--bold">Enlaces a nuestra Pagina Web</p>
        <p><a href="/">Inicio</a></p>
        <p><a href="/Productos">Productos</a></p>
        <p>©<?php echo date('Y')?> CyberArt, todos los derechos reservados.</p>
    </div>    
</footer>
<script src="build/js/landingPage.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>