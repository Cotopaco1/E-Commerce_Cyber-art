<?php 

namespace Controller;

use Classes\Email;
use Model\Items_pedido;
use Model\Pedidos;
use Model\Productos;
use Model\Usuarios;

class ControllerApi{

    public static function getCuadros(){

        $cuadros = Productos::all();

        echo(json_encode($cuadros));
    }
    public static function crear_orden(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);

            //Guardar registro del usuario....
            $usuario = new Usuarios($datos['usuario']);
            $alertas = $usuario->validar_datos_para_formulario_crear_pedido();

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
            $usuario->validar_datos_para_formulario_crear_pedido();

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

    public static function crear_cuenta(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $error['error'] =  false;
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);

            //crear usuario con los datos mandados desde el front-end
            $usuario = new Usuarios($datos);
            $alertas = $usuario->validarNuevaCuenta();
            //Si no tiene password confirmar entonces hay error.
            if(!$datos['password_confirmar']){
                $alertas['error'] = 'La password de confirmar no existe..';
            }
            $alertas = $usuario->comprobar_igualdad_de_password($datos['password_confirmar']);
            $resultado = $usuario->userExist();
            /* echo json_encode($resultado->num_rows);
            return; */
            if($resultado->num_rows > 0){
                $alertas['error'][] = 'El usuario existe';
            }
            if(empty($alertas)){
                $usuario->hashearPassword();
                $usuario->crearToken();
                $resultado = $usuario->guardar();

                if($resultado['resultado']){
                    //enviar email
                    $mail = new Email($usuario->email, $usuario->nombre, $usuario->token );
                    $resultadoEmail = $mail->enviarEmail('confirmacion');

                    $respuesta = [
                        'respuesta'=> true,
                        'mensaje'=> "Cuenta creada exitosamente, un correo de confirmacion ha sido enviado a $usuario->email",
                        'alertas'=> $alertas,
                        'resultadoEmail'=> $resultadoEmail
                    ];
                    echo json_encode($respuesta);
                    return;
                }else{
                    $respuesta = [
                        'respuesta'=>false,
                        'mensaje'=> 'No se ha podido guardar la cuenta en la base de datos'
                    ];
                }

            }
            if(!empty($alertas['error'])){
                $resultado = [
                    'alertas'=> $alertas,
                    'error'=> 'Ha habido un error...'
                ];
            }
            //Si existe un error entonces imprimir ese error.
            if($error['error']){

                echo json_encode($error);
                return;
            }


            echo json_encode($resultado);
        }
       

    }

    public static function logIn_usuario(){

        if($_SERVER['REQUEST_METHOD']  === 'POST'){

            $error['error'] =  false;
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);

            $usuarioPost = new Usuarios($datos);
            $alertas = $usuarioPost->validarLogin();

            if(empty($alertas)){
                
                $resultado = Usuarios::where_con_sentencia_preparada('email', $usuarioPost->email);

                if($resultado->num_rows === 0){
                    $respuesta = ['resultado'=>'error', 'mensaje'=>'El email no existe'];
                    echo json_encode($respuesta);
                    return;
                }
                $usuarioDB = new Usuarios($resultado->fetch_assoc());

                $resultado = $usuarioDB->comprobarPasswordAndVerificado($usuarioPost->password);
                if(!$resultado){
                    $respuesta = [
                        'respuesta'=>'error',
                        'mensaje'=> 'La contraseña es incorrecta'
                    ];
                    echo json_encode($respuesta);
                    return;
                } 

                //dar acceso al usuario 
                iniciar_sesion_sino_esta_iniciada();
                $_SESSION['id'] = $usuarioDB->id;
                $_SESSION['email'] = $usuarioDB->email;
                $_SESSION['nombre'] = $usuarioDB->nombre . " " . $usuarioDB->apellido;
                $_SESSION['login'] = true;

                $respuesta = [
                    'respuesta'=> 'exitoso',
                    'mensaje'=> 'logIn exitoso',
                    'sesionInfo'=> $_SESSION
                ];
                echo json_encode($respuesta);
                return;
            }
            $respuesta = [
                'respuesta'=>'error',
                'alertas'=> $alertas
            ];
            echo json_encode($respuesta);
            return;
        }
    }
  

}