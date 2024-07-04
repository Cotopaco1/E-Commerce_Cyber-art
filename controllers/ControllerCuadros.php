<?php 

namespace Controller;

use Model\Items_pedido;
use Model\Pedidos;
use Model\Productos;
use Model\Usuarios;

class ControllerCuadros{

    public static function getCuadros(){

        $cuadros = Productos::all();

        echo(json_encode($cuadros));
    }
    public static function guardarOrden(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);

            //Guardar registro del usuario....
            $usuario = new Usuarios($datos['usuario']);
            $alertas = $usuario->validar();

            if(!empty($alertas['error'])){
                $respuesta = [
                    'error'=> 'No se ha podido validar el usuario',
                    'alertas'=> $alertas
                ];
                echo json_encode($respuesta);
                return;
            }
            $resultado = $usuario->guardar();

            if(!$resultado){
                $respuesta = [
                    'error'=> 'No se ha podido guardar el usuario',
                    'resultado'=> $resultado
                ];
                echo json_encode($respuesta);
                return;
            }
            //Guardar registro del pedido
            $pedido = new Pedidos($datos['pedidos']);
            $pedido->status = 'pendiente';
            $pedido->usuarioID = $resultado['id'];
            $alertas = $pedido->validar();
            //Si no hay errores entonces:
            if(empty($alertas['error'])){
                $resultado = $pedido->guardar();

                if($resultado['resultado']){
                    $pedidosId = $resultado['id'];
    
                    foreach ($datos['carritoDeCompra'] as $producto) {
                        $items_pedido = new Items_pedido($producto);
                        $items_pedido->pedidosId = $pedidosId;
                        $items_pedido->productosId = $producto['id'];
                        $items_pedido->id = NULL;
                        $items_pedido->precio_total = $producto['precio'] * $producto['cantidad'] ;
                        $alertas = $items_pedido->validar();
                        if(empty($alertas['error'])){
                            $resultado = $items_pedido->guardar();

                            if($resultado['resultado']){
                                
                                $respuesta = [
                                    'exito'=> 'se ha guardado todo en la DB',
                                    'pedidoId'=> $pedidosId,
                                    'nombre'=> "$usuario->nombre $usuario->apellido",
                                    'costo_total'=> $pedido->monto_total,
                                    'direccion'=> "$pedido->direccion en $pedido->departamento - $pedido->ciudad ",
                                    
                                ];
                            }
                        }
                    }
                    
                }else{
                    $respuesta = [
                        'error'=> 'pedido no se pudo guardar..'
                    ];
                }
            }else{
                $respuesta = [
                    'error' => 'Pedidos tuvo errores',
                    'alertas' => $alertas
                ];

            }
            /* $pedido->usuarioID = $datos['userData']['usuarioID']; */
            /* $resultado = $pedido->guardar(); */

            //Guarda un registro por cada producto que haya en carrito de compra.
           
            
        }

        echo json_encode($respuesta);
    }

    public static function guardar_carrito_de_compras_en_session(){

        iniciar_sesion_sino_esta_iniciada();

        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if(!isset($_SESSION['datos'])){
                $respuesta = ['resultado'=> false, 'mensaje'=>'Error, no hay carrito de compra',
            'sessionDatos'=> $_SESSION];
                echo json_encode($respuesta);
                return;
            }
            $respuesta = $_SESSION['datos'];
            echo json_encode($respuesta);
            return;
        }

            
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            /* if(is_null(file_get_contents('php://input'))){
            } */
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);
            $_SESSION['datos'] = $datos;
            if($_SESSION['datos'] !== ''){
                $respuesta = [
                    'resultado'=> true
                ];
            }else{
                $respuesta = [
                    'resultado' => false
                ];
            }
            echo json_encode($respuesta);
            return;
        }
    }

    public static function validarFormulario(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);


            $alertas = [];
            //Validar nombre, apellido, telefono, email
            $usuario = new Usuarios($datos);
            $usuario->validar();

            //validar direccion, departamento, ciudad.
            $pedido = new Pedidos($datos);
            $alertas = $pedido->validar_direccion();
            /* $ciudad = $datos['ciudad'];
            $departamento = $datos['departamento'];
            $direccion = $datos['direccion']; */


            if(empty($alertas)){
                //No hay errores entonces permitir pasarlo a la siguiente seccion....
                $alertas = [
                    'exito'=>'El usuario ha pasado la validacion'
                ];
                echo json_encode($alertas);
                return;
            }else{

                echo json_encode($alertas);
                return;
            }
            

            //Guardar los datos ya validados en la clase de pedidos...
        }
    }

    public static function crear_orden(){
        iniciar_sesion_sino_esta_iniciada();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            /* if(is_null(file_get_contents('php://input'))){
            } */
                //extraemos el archivo json enviado..
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);
            $_SESSION['datos'] = $datos;
            if($_SESSION['datos'] !== ''){
                $respuesta = [
                    'resultado'=> true,
                    'server'=> $_SERVER
                ];
            }else{
                $respuesta = [
                    'resultado' => false,
                    'server'=> $_SERVER
                ];
            }
            echo json_encode($respuesta);
            return;
        }
    }
}