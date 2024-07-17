<?php 

namespace Controller;

use Classes\Email;
use Model\Info_admin;
use Model\Items_pedido;
use Model\Pedidos;
use Model\Productos;
use Model\Usuarios;
use Model\PedidosAdmin;
//Image intervention
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

//End image intervention

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

                if($usuarioDB->admin === 1){
                    $_SESSION['admin'] = true;
                }

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
    public static function reenviar_email(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);

            $resultado = Usuarios::where_con_sentencia_preparada('email', $datos['email']);
            if($resultado->num_rows === 0){
                $respuesta = [
                    'respuesta'=>'error',
                    'mensaje'=>'El correo no existe'
                ];
                echo json_encode($respuesta);
                return;
            }
            $usuario = new Usuarios($resultado->fetch_assoc());
            if($usuario->confirmado === 1){
                $respuesta = [
                    'respuesta'=>'error',
                    'mensaje'=>'El correo ya se encuentra confirmado'
                ];
                echo json_encode($respuesta);
                return;
            }
            //si llego aca significa que el correo existe y no esta confirmado..
            $usuario->crearToken();
            $usuario->guardar();
            $mail = new Email($usuario->email, $usuario->nombre, $usuario->token);
            $resultado = $mail->enviarEmail('confirmacion');
            if($resultado === true){
                $respuesta = [
                    'respuesta'=>'exito',
                    'mensaje'=>"Hemos reenviado el correo a $mail->email , revisa en tu bandeja de entrada"
                ];
                echo json_encode($respuesta);
                return;
            }else{
                $respuesta = [
                    'respuesta'=>'error',
                    'mensaje'=>'No se pudo reenviar el correo...'
                ];
                echo json_encode($respuesta);
                return;
            }

        }
        
    }
    public static function recuperar_password(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);

            $resultado = Usuarios::where_con_sentencia_preparada('email', $datos['email']);
            if($resultado->num_rows === 0){
                $respuesta = [
                    'respuesta'=>'error',
                    'mensaje'=>'El correo no existe'
                ];
                echo json_encode($respuesta);
                return;
            }
            $usuario = new Usuarios($resultado->fetch_assoc());

            //si llego aca significa que el correo existe.
            $usuario->crearTokenResetPassword();
            $resultado = $usuario->guardar();
            
            $mail = new Email($usuario->email, $usuario->nombre, $usuario->token_reset_password);
            $resultado = $mail->enviarEmail('olvide');
            if($resultado === true){
                $respuesta = [
                    'respuesta'=>'exito',
                    'mensaje'=>"Hemos enviado instrucciones al correo $mail->email , revisa en tu bandeja de entrada"
                ];
                echo json_encode($respuesta);
                return;
            }else{
                $respuesta = [
                    'respuesta'=>'error',
                    'mensaje'=>$resultado
                ];
                echo json_encode($respuesta);
                return;
            }

        }
    }
    public static function reestablecer_password(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);

            $resultado = Usuarios::where_con_sentencia_preparada('token_reset_password', $datos['token_reset_password']);

            if($resultado->num_rows === 0){
                $respuesta = [
                    'respuesta'=> 'error',
                    'mensaje'=> 'Token invalido'
                ];
                echo json_encode($respuesta);
                return;
            }
            //Si las dos passwords no son iguales entonces return
            if($datos['password'] !== $datos['password_confirmar']){
                $respuesta = [
                    'respuesta'=> 'error',
                    'mensaje'=> 'Las passwords no coinciden'
                ];
                echo json_encode($respuesta);
                return;
            }
            //existe usuaro y las passwords recibidas son iguales.
            //existe usuario
            $usuario = new Usuarios($resultado->fetch_assoc());
            $usuario->password = $datos['password'];

            $alertas = $usuario->validarPassword();

            if(empty($alertas)){
                $usuario->token_reset_password = '';
                $usuario->hashearPassword();
                $resultado = $usuario->guardar();
                
                if($resultado){
                    $respuesta = [
                        'respuesta'=> 'exito',
                        'mensaje'=> 'La password ha sido reestablecida correctamente'
                    ];
                    echo json_encode($respuesta);
                    return;
                }else{
                    $respuesta = [
                        'respuesta'=> 'error',
                        'mensaje'=> 'No se pudo reestablecer correctamente'
                    ];
                    echo json_encode($respuesta);
                }

            }
            else{
                $respuesta = [
                    'respuesta'=> 'error',
                    'mensaje'=> 'Las passwords no coinciden'
                ];
                echo json_encode($respuesta);
            }


        }

        /* $respuesta = [
            'debugear'=>$datos
        ];
        echo json_encode($respuesta); */
    }

    public static function get_info_resumen(){

        $query = "SELECT ";
        $query .= "(SELECT COUNT(*) FROM pedidos) AS cantidad_pedidos, ";
        $query .= "(SELECT COUNT(*) FROM productos) AS cantidad_productos, ";
        $query .= "(SELECT COUNT(*) FROM usuarios) AS cantidad_usuarios";

        $info_admin = Info_admin::SQL($query);

        imprimirJson($info_admin[0]);    
    
    }

    //CRUD
    public static function actualizar_producto(){
        if($_SERVER['REQUEST_METHOD']==='POST'){
            /* $data = file_get_contents('php://input');
            $datos = json_decode($data,true); */

            $id = $_POST['id'];
            $productoDB = Productos::find($id);
            $productoPOST = new Productos($_POST);
            $productoPOST->imagen = null;
            $productoDB->sincronizar($productoPOST);
            
            /* imprimirJson($productoDB);
            return; */
            //Si hay una imagen, entonces...
            if($_FILES['imagen']['error'] === 0){
                $manager = new ImageManager(Driver::class);
                $imagen_file = $_FILES['imagen'];
                $imagen = $manager->read($imagen_file['tmp_name']);
                
                $nombreImagen = md5( uniqid( rand(), true)) . '.jpeg';
                $destino = __DIR__ . '/../public/img/productos/'. $nombreImagen;

                $imagen->cover(400,600);
                $imagen->toJpeg();
                $imagen->save($destino);

                $productoDB->setImagen($nombreImagen);
            }
            //Validamos que no haya un error antes de guardar.
            $alertas = $productoDB->validar();
                if(empty($alertas)){
                    $resultado = $productoDB->guardar();
                    if($resultado){
                        echo json_encode(['resultado'=>true,
                        'mensaje'=> 'Producto actualizado correctamente'
                    ]);
                        return;
                    }
                }else{
                echo json_encode([
                    'resultado'=>false,
                    'alertas'=> $alertas['error']
                ]);
                return; 
                }
                          
        }

    }
    public static function eliminar_producto(){
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);
            $id = $datos['id'];
            $producto = Productos::find($id);
            $resultado = $producto->eliminar();
            if($resultado){
                echo json_encode([
                    'resultado'=>true,
                    'mensaje'=> 'El producto ha sido eliminado exitosamente'
                ]);
                return;
            }else{
                echo json_encode([
                    'resultado'=>false,
                    'mensaje'=> 'El producto no se ha podido eliminar'
                ]);
                return;
            }
            
        }
    }
    public static function crear_producto(){

        if($_SERVER['REQUEST_METHOD']=== 'POST'){
            $producto = new Productos($_POST);
            if($_FILES['imagen']['error'] !== 0){
                echo json_encode([
                    'resultado'=>false,
                    'alertas'=> ['Imagen no recibida']
                ]);
                exit;
            }
            //setear imagen.
            $manager = new ImageManager(Driver::class);
            $imagen_file = $_FILES['imagen'];
            $imagen = $manager->read($imagen_file['tmp_name']);
            
            $nombreImagen = md5( uniqid( rand(), true)) . '.jpeg';
            $destino = __DIR__ . '/../public/img/productos/'. $nombreImagen;
            $producto->setImagen($nombreImagen);
            $alertas = $producto->validar();
            if(!empty($alertas)){
                echo json_encode([
                    'resultado'=>false,
                    'alertas'=>$alertas['error']
                ]);
                exit;
            }
            $resultado = $producto->guardar();
            if(!$resultado){
                echo json_encode([
                    'resultado'=>false,
                    'alertas'=>['No se ha podido guardar el producto']
                ]);
                exit;
            }
            $imagen->cover(400,600);
            $imagen->toJpeg();
            $imagen->save($destino);

            echo json_encode([
                'resultado'=>true,
                'mensaje'=>'El producto ha sido creado con exito'
            ]);
            exit;

        }
    }
    //Pedidos
    public static function get_pedidos(){
        $query = "SELECT pedidos.id,pedidos.fecha,pedidos.status,pedidos.monto_total as total,";
        $query .= "CONCAT(usuarios.nombre,' ', usuarios.apellido) as nombre,";
        $query .= "usuarios.cedula,usuarios.email, usuarios.telefono, ";
        $query .= "CONCAT(pedidos.direccion, ' ', pedidos.departamento, ' ', pedidos.ciudad) as direccion,";
        $query .= "pedidos.metodo_pago,pedidos.informacion_adicional, items_pedido.productosId as productoId ";
        $query .= "FROM pedidos INNER JOIN usuarios ON pedidos.usuarioID=usuarios.id ";
        $query .= "INNER JOIN items_pedido ON items_pedido.pedidosId=pedidos.id";
        
        // $pedidos = PedidosAdmin::SQL($query);
        $pedidos = PedidosAdmin::SQL($query);

        imprimirJson($pedidos);  
    }
    public static function get_pedidos_where(){
        $fecha = $_GET['fecha'];

        $query = "SELECT pedidos.id,pedidos.fecha,pedidos.status,pedidos.monto_total as total,";
        $query .= "CONCAT(usuarios.nombre,' ', usuarios.apellido) as nombre,";
        $query .= "usuarios.cedula,usuarios.email, usuarios.telefono, ";
        $query .= "CONCAT(pedidos.direccion, ' ', pedidos.departamento, ' ', pedidos.ciudad) as direccion,";
        $query .= "pedidos.metodo_pago,pedidos.informacion_adicional, items_pedido.productosId as productoId ";
        $query .= "FROM pedidos INNER JOIN usuarios ON pedidos.usuarioID=usuarios.id ";
        $query .= "INNER JOIN items_pedido ON items_pedido.pedidosId=pedidos.id ";
        $query .= "WHERE fecha = '$fecha'";
        // $pedidos = PedidosAdmin::SQL($query);
        /* debugear($query); */
        $pedidos = PedidosAdmin::SQL($query);

        echo json_encode([
            'respuesta'=>$pedidos
        ]);
        exit;
        /* imprimirJson($pedidos);  */ 
    }
}