<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../build/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Panel de administracion</title>

</head>
<body class="body_admin">
    <aside class="menu_administrador">
        <div class="menu_campos">
            <div class="campo_menu titulo_menu">
                <h3>Menu</h3>
            </div>
            <div class="campo_menu">
                <i class="fa-solid fa-house fa-2xl" id="home"></i>
            </div>
            <div class="campo_menu">
                <i class="fa-solid fa-image-portrait fa-2xl" id="productos"></i>
            </div>
            <div class="campo_menu">
                <i class="fa-solid fa-plus fa-2xl" id="nuevo_producto"></i>
            </div>
            <div class="campo_menu">
                <i class="fa-solid fa-scroll fa-2xl"></i>
            </div>
        </div>
        <div class="menu_footer">
            <div class="campo_menu">
                <i class="fa-solid fa-gear"></i>
            </div>
        </div>

    </aside>
    <div class="contenido_admin">
        <div class="contenido_header">
            <div id="nombre_admin" class="nombre_admin">
                <p><?php echo $nombre ?></p>
            </div>
            <div id="cerrar_sesion"><a href="#">Cerrar Sesion</a></div>
        </div>
        <div class="app">
            <div class="titulo_app"><h1>Resumen</h1></div>
            <main class="main_admin">

                <?php echo $contenido ?>
            </main>
        </div>

        <footer class="footer ">
        <p>Copyright <?php echo date('Y'); ?>, designed by Sergio Silva</p>
        </footer>
    </div>



    <script src="../build/js/admin.js" type="module"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>