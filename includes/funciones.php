<?php

define('CARPETA_IMAGENES_PRODUCTOS',
$_SERVER['DOCUMENT_ROOT'] . '/img/productos');


function debugear($variable){
    echo '<pre>';
    var_dump($variable);
    echo '</pre>';
    exit;
}

function iniciar_sesion_sino_esta_iniciada(){
    if(!isset($_SESSION)){
        session_start();
        /* $_SESSION['carrito_de_compras'] =  */
    }
}
function verificar_admin(){
    iniciar_sesion_sino_esta_iniciada();
    if(!isset($_SESSION['admin']) || !$_SESSION['admin']){
        header('location: /');
        exit;
    }
}

function imprimirJson($variable){
    echo json_encode(['respuesta'=>$variable]);
    return;
}
function imprimirRespuestaJson($tipo,$mensaje){
    echo json_encode([
        $tipo=>$mensaje
    ]);
    exit;
}