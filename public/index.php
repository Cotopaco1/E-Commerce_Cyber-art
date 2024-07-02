<?php

require '../includes/app.php';

use Controller\ControllerCuadros;
use Controller\ControllerPaginas;
use MVC\Router;

$router = new Router();

$router->get('/', [ControllerPaginas::class, 'index']);
$router->get('/carrito_de_compras', [ControllerPaginas::class, 'carrito_de_compras']);



//Api...
$router->get('/api/cuadros', [ControllerCuadros::class, 'getCuadros']);
$router->post('/api/cuadros', [ControllerCuadros::class, 'guardarOrden']);



$router->comprobarRutas();