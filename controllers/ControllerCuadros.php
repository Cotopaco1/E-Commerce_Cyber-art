<?php 

namespace Controller;

use Model\Items_pedido;
use Model\Pedidos;
use Model\Productos;

class ControllerCuadros{

    public static function getCuadros(){

        $cuadros = Productos::all();

        echo(json_encode($cuadros));
    }
    public static function guardarOrden(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);

            //Guardar registro del pedido
            $pedido = new Pedidos($datos);
            $pedido->usuarioID = $datos['userData']['usuarioID'];
            $resultado = $pedido->guardar();

            //Guarda un registro por cada producto que haya en carrito de compra.
            $pedidosId = $resultado['id'];
            foreach ($datos['carritoDeCompra'] as $producto) {
                $items_pedido = new Items_pedido($producto);
                $items_pedido->pedidosId = $pedidosId;
                $items_pedido->productosId = $producto['id'];
                $items_pedido->id = NULL;
                $items_pedido->precio_total = $producto['precio'] * $producto['cantidad'] ;
                $resultado = $items_pedido->guardar();
            }
            
        }

        echo json_encode($resultado);
    }
}