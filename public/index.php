<?php

require '../includes/app.php';

use Controller\ControllerCuadros;
use Controller\ControllerPaginas;
use MVC\Router;

$router = new Router();

$router->get('/', [ControllerPaginas::class, 'index']);
$router->get('/carrito_de_compras', [ControllerPaginas::class, 'carrito_de_compras']);
$router->post('/carrito_de_compras', [ControllerPaginas::class, 'carrito_de_compras']);



//Api...
$router->get('/api/cuadros', [ControllerCuadros::class, 'getCuadros']);
$router->post('/api/cuadros', [ControllerCuadros::class, 'guardarOrden']);
$router->post('/api/cuadros/guardar_carrito_compras', [ControllerCuadros::class, 'guardar_carrito_de_compras_en_session']);
$router->get('/api/cuadros/guardar_carrito_compras', [ControllerCuadros::class, 'guardar_carrito_de_compras_en_session']);

$router->post('/api/cuadros/crear_orden', [ControllerCuadros::class, 'guardarOrden']);
$router->post('/api/cuadros/validar_formulario', [ControllerCuadros::class, 'validarFormulario']);



$router->comprobarRutas();