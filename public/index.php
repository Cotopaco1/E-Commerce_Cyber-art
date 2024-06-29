<?php

require '../includes/app.php';

use Controller\ControllerCuadros;
use Controller\ControllerPaginas;
use MVC\Router;

$router = new Router();

$router->get('/', [ControllerPaginas::class, 'index']);

//Api...
$router->get('/api/cuadros', [ControllerCuadros::class, 'getCuadros']);


$router->comprobarRutas();