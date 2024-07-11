<?php

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

function imprimirJson($variable){

    echo json_encode(['respuesta'=>$variable]);
    return;

}