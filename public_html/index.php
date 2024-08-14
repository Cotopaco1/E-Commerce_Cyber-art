<?php

require '../includes/app.php';

use Controller\ControllerAdmin;
use Controller\ControllerApi;
use Controller\ControllerLandingPages;
use Controller\ControllerLogin;
use Controller\ControllerPaginas;
use MVC\Router;

$router = new Router();

//Paginas publicas
$router->get('/', [ControllerPaginas::class, 'index']);
$router->get('/carrito_de_compras', [ControllerPaginas::class, 'carrito_de_compras']);
$router->post('/carrito_de_compras', [ControllerPaginas::class, 'carrito_de_compras']);
$router->get('/producto', [ControllerPaginas::class, 'producto']);
$router->get('/productos', [ControllerPaginas::class, 'productos']);

//LandingPages
$router->get('/rickAndMorty', [ControllerLandingPages::class, 'rickAndMorty']);




//Login
$router->get('/login', [ControllerLogin::class, 'index']);
$router->post('/login', [ControllerLogin::class, 'index']);
    //Crear cuenta
$router->get('/crear_cuenta', [ControllerLogin::class, 'crear_cuenta']);
$router->post('/crear_cuenta', [ControllerLogin::class, 'crear_cuenta']);
    //Recuperar Cuenta
$router->get('/recuperar_password', [ControllerLogin::class, 'recuperar_password']);
$router->get('/reestablecer_password', [ControllerLogin::class, 'reestablecer_password']);

    //Confirmar cuenta
$router->get('/confirmar_cuenta', [ControllerLogin::class, 'confirmar_cuenta']);
    //reenviar email de confirmar cuenta.
$router->get('/reenviar_email', [ControllerLogin::class, 'reenviar_email']);
$router->post('/reenviar_email', [ControllerLogin::class, 'reenviar_email']);
     
    //logOut
$router->get('/logout', [ControllerLogin::class, 'log_out']);



//Paginas privadas...

$router->get('/admin/home', [ControllerAdmin::class, 'index']);


//Api...
    //carrito_de_compras
$router->get('/api/cuadros/getcuadros', [ControllerApi::class, 'getCuadros']);
$router->get('/api/get/cuadro', [ControllerApi::class, 'cuadro']);
$router->post('/api/cuadros/guardar_carrito_compras', [ControllerApi::class, 'guardar_carrito_de_compras_en_session']);
$router->post('/api/session/guardar_carrito', [ControllerApi::class, 'guardar_carrito']);
$router->get('/api/session/get_carrito', [ControllerApi::class, 'get_carrito']);
$router->get('/api/cuadros/guardar_carrito_compras', [ControllerApi::class, 'guardar_carrito_de_compras_en_session']);
$router->post('/api/validar_formulario', [ControllerApi::class, 'validarFormulario']);
$router->post('/api/crear_orden', [ControllerApi::class, 'crear_orden']);
    //formulario index
$router->post('/api/formulario_cuadro_personalizado',[ControllerApi::class, 'formulario_cuadro_personalizado']);

    //Login
$router->post('/api/login/crear_cuenta', [ControllerApi::class, 'crear_cuenta']);
$router->post('/api/login/login', [ControllerApi::class, 'logIn_usuario']);
$router->post('/api/login/reenviar_email', [ControllerApi::class, 'reenviar_email']);
$router->post('/api/login/recuperar_email', [ControllerApi::class, 'recuperar_password']);
$router->post('/api/login/reestablecer_password', [ControllerApi::class, 'reestablecer_password']);

    //admin
$router->get('/api/admin/get_info_resumen', [ControllerApi::class, 'get_info_resumen']);
$router->get('/api/admin/get_pedidos', [ControllerApi::class, 'get_pedidos']);
$router->get('/api/admin/get_pedidos_where', [ControllerApi::class, 'get_pedidos_where']);
$router->get('/api/admin/get_pedidos_filtrados', [ControllerApi::class, 'get_pedidos_filtrados']);
$router->get('/api/admin/get_cuadros_where_no_deleted', [ControllerApi::class, 'get_cuadros_where_no_deleted']);

$router->post('/api/admin/pedido/actualizar_estado', [ControllerApi::class, 'actualizar_estado_pedido']);
$router->post('/api/admin/actualizar_producto', [ControllerApi::class, 'actualizar_producto']);
$router->post('/api/admin/eliminar_producto', [ControllerApi::class, 'eliminar_producto']);
$router->post('/api/admin/crear_producto', [ControllerApi::class, 'crear_producto']);

//landingPages
$router->post('/api/lp/enviar_formulario', [ControllerApi::class, 'formulario_landing_page']);
$router->get('/api/lp/cuenta_regresiva', [ControllerApi::class, 'cuenta_regresiva']);

$router->comprobarRutas();