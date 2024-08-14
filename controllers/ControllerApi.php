<?php 

namespace Controller;

use Classes\Email;
use DateTime;
use Model\Info_admin;
use Model\Items_pedido;
use Model\Pedidos;
use Model\Productos;
use Model\Usuarios;
use Model\PedidosAdmin;
//Image intervention
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Model\FormularioLandingPage;
use Model\FormularioPersonalizado;
use Model\ProductosComprados;

//End image intervention

class ControllerApi{

    public static function getCuadros(){
        if($_SERVER['REQUEST_METHOD']=== "GET"){
            //Si no hay queryString regresamos TODOs
            if(!$_GET){
                $cuadros = Productos::all_no_deleted_and_disponibles();
                echo(json_encode($cuadros));    
                exit;
            }
            //devolver la cantidad que quiera...
            $cantidad = $_GET['n'];
            if(is_numeric($cantidad)){
                //buscar la cantidad de cuadros
                $cuadros = Productos::get_not_deleted_and_disponible($cantidad);
                echo(json_encode($cuadros));
                exit; 
            }else{
                echo json_encode([
                    'error'=>'url invalida'
                ]);
                exit;
            }

        }
    }
    public static function get_cuadros_where_no_deleted(){
        if($_SERVER['REQUEST_METHOD']==='GET'){
            verificar_admin();
            $cuadros = Productos::all_no_deleted();
            echo(json_encode($cuadros));    
            exit;
        }
    }
    public static function cuadro(){
        $id = is_numeric($_GET['id']);
        if(!$id){
            echo json_encode([
                'respuesta'=>false,
                'mensaje'=>'Id invalido'
            ]);
            return;
        }
        $producto = Productos::where('id', $_GET['id']);
        echo json_encode([
            'respuesta'=>true,
            'producto'=>$producto
        ]);
        return;


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
            //monto_total
            $monto_total= 0;
            //itero sobre carrito de compra para tener suma total
            foreach ($datos['carritoDeCompra'] as $producto) {
                $productoDB = Productos::where('id',$producto['id']);
                if(!$productoDB){
                    $pedido::setAlerta('error','producto no encontrado');
                    break;
                }
                $monto_total = $monto_total + ($productoDB->precio * $producto['cantidad']);
            }
            $pedido->monto_total = $monto_total;

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
                        }
                    }
                    //dar respuesta
                    if($resultado['resultado']){
                        $SQL = "SELECT productos.nombre, items_pedido.cantidad FROM items_pedido INNER JOIN productos ON items_pedido.productosId = productos.id  WHERE pedidosId = {$pedidosId}";
                        $productos = ProductosComprados::SQL($SQL);
                        // $productos = Pedidos::belongsTo('pedidosId',$resultado['id']);
                        if(!$productos){
                            echo json_encode([
                                'error'=>'No se pudo encontrar los productos'
                            ]);
                            exit;
                        }
                        //Enviar correo electronico al administrador y al correo del usuario.
                        $email = new Email($usuario->email,$usuario->nombre);
                        $email_resultado = $email->enviar_email_nueva_orden($pedidosId,"$usuario->nombre $usuario->apellido",
                        $pedido->monto_total,
                        "$pedido->direccion en $pedido->departamento - $pedido->ciudad ",
                        $productos
                        );
                         $_SESSION['productos'] = [];
                        
                        $respuesta = [
                            'exito'=> 'se ha guardado todo en la DB',
                            'pedidoId'=> $pedidosId,
                            'nombre'=> "$usuario->nombre $usuario->apellido",
                            'costo_total'=> $pedido->monto_total,
                            'direccion'=> "$pedido->direccion en $pedido->departamento - $pedido->ciudad ",
                            'productos'=> $productos,
                            'email_enviado'=>$email_resultado,
                            'carrito'=>$_SESSION['productos']
                  
                        ];
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

    public static function guardar_carrito(){
        if($_SERVER['REQUEST_METHOD']==='POST'){
            iniciar_sesion_sino_esta_iniciada();
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);
            $_SESSION['productos'] = $datos;
            echo json_encode([
                'respuesta'=>true
            ]);
            return;
        }
    }
    public static function get_carrito(){
        if($_SERVER['REQUEST_METHOD']==='GET'){
            iniciar_sesion_sino_esta_iniciada();

            if(isset($_SESSION['productos'])){
                echo json_encode([
                    'resultado'=>true,
                    'productos'=>$_SESSION['productos']
                ]);
                return;
            }else{
                echo json_encode([
                    'resultado'=>false,
                    'mensaje'=>'No hay productos en el carrito'
                ]);
            }
        
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
    //Formulario para cuadro personalizado
    public static function formulario_cuadro_personalizado(){
        if($_SERVER['REQUEST_METHOD']==='POST'){
            //validar formulario
            $formulario = new FormularioPersonalizado($_POST);
            $formulario->validar();
            if($formulario->contacto === 'email'){
                $formulario->validar_email();
            }
            $alertas = FormularioPersonalizado::getAlertas();
            if(empty($alertas)){
                //juntar el nombre y apellido en el nombre de la clase.
                $formulario->nombre = $formulario->nombre . " ". $formulario->apellido;
                //lleno la variable contacto con email o telefono, dependeidno de lo que el usuario haya elegido
                ($formulario->contacto === 'email')? $contacto = $formulario->email : $contacto =$formulario->telefono;
                $email = new Email(
                    $contacto,
                    $formulario->nombre,
                    '',
                    $formulario->mensaje
                );
                $resultado = $email->enviar_email_cuadro_personalizado_al_administrador();
                if($resultado){
                    echo json_encode([
                        'tipo'=>'exito',
                        'mensaje'=>'Correo enviado correctamente'
                    ]);
                    exit;
                }else{
                    echo json_encode([
                        'error'=>'No se pudo realizar el envio del correo'
                    ]);
                    exit;
                }

            }else{
                echo json_encode([
                    'tipo'=>'error',
                    'alertas'=>$alertas
                ]);
                exit;
            }

        }

    }
    //termina formulario para cuadro personalizado

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
                //guardo los resultados en una variable
                $usuarioDBARRAY = $resultado->fetch_assoc();
                //creo una instancia para usar metodos
                $usuarioDB = new Usuarios($usuarioDBARRAY);
                //al instanciar el admin se pone en 0 por default
                //por eso formateo el valor al original con el array que habia guardado los datos originales.
                $usuarioDB->admin = $usuarioDBARRAY['admin'];
                
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
            //traigo info de la DB para reescribir admin y confirmado REAL.
            $usuarioDB = Usuarios::where('email',$usuario->email);
                
            $usuario->admin = $usuarioDB->admin;
            $usuario->token = $usuarioDB->token;
            $usuario->confirmado = $usuarioDB->confirmado;
            
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
                //traigo info de la DB para reescribir admin y confirmado REAL.
                $usuarioDB = Usuarios::where('email',$usuario->email);
                
                $usuario->admin = $usuarioDB->admin;
                $usuario->token = $usuarioDB->token;
                $usuario->confirmado = $usuarioDB->confirmado;
                
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
        verificar_admin();
        $query = "SELECT ";
        $query .= "(SELECT COUNT(*) FROM pedidos) AS cantidad_pedidos, ";
        $query .= "(SELECT COUNT(*) FROM productos) AS cantidad_productos, ";
        $query .= "(SELECT COUNT(*) FROM usuarios) AS cantidad_usuarios";

        $info_admin = Info_admin::SQL($query);

        imprimirJson($info_admin[0]);    
    
    }

    //CRUD
    public static function actualizar_producto(){
        verificar_admin();
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
        verificar_admin();

        if($_SERVER['REQUEST_METHOD']==='POST'){
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);
            $id = $datos['id'];
            if(!is_numeric($datos['id'])) return;
            $producto = Productos::find($id);
            $resultado = $producto->eliminar_soft();
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
        verificar_admin();
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
    //Admin
    public static function get_pedidos(){
        verificar_admin();
        if($_SERVER['REQUEST_METHOD']==='GET'){
            /* $query = "SELECT pedidos.id,pedidos.fecha,pedidos.status,pedidos.monto_total as total,";
            $query .= "CONCAT(usuarios.nombre,' ', usuarios.apellido) as nombre,";
            $query .= "usuarios.cedula,usuarios.email, usuarios.telefono, ";
            $query .= "CONCAT(pedidos.direccion, ' ', pedidos.departamento, ' ', pedidos.ciudad) as direccion,";
            $query .= "pedidos.metodo_pago,pedidos.informacion_adicional, items_pedido.productosId as productoId, productos.nombre as productoNombre, items_pedido.cantidad as productoCantidad ";
            $query .= "FROM pedidos INNER JOIN usuarios ON pedidos.usuarioID=usuarios.id ";
            $query .= "INNER JOIN items_pedido ON items_pedido.pedidosId=pedidos.id ";
            $query .= "INNER JOIN productos ON items_pedido.productosId=productos.id"; */
            
            // $pedidos = PedidosAdmin::SQL($query);
            $pedidos = PedidosAdmin::all();

            imprimirJson($pedidos); 
        }
         
    }
    public static function get_pedidos_filtrados(){
        verificar_admin();
        if($_SERVER['REQUEST_METHOD']==='GET'){
            

            $fecha = $_GET['fecha'];
            $estado = $_GET['estado'];
            if(!$fecha && !$estado){
                imprimirRespuestaJson('error','parametros invalidos');
            }
            if(!$fecha){
                //si no existe fecha, traemos filtrado por estado.
                if($estado === 'todos'){
                    //si estado es todos y fecha no existe traemos
                    //traemos todos los pedidos.
                    $pedidos = PedidosAdmin::all();
                    echo json_encode([
                        'pedidos'=>$pedidos,
                        'exito'=>'pedidos traidos con exito'
                    ]);
                exit;
                }
                $pedidos = PedidosAdmin::where('status',$estado);
                echo json_encode([
                    'pedidos'=>$pedidos,
                    'exito'=>'pedidos traidos con exito'
                ]);
                exit;
            }

            if(!$estado || $estado === 'todos'){
                //si no existe estado entonces traemos filtrado por fecha
                $pedidos = PedidosAdmin::where('fecha',$fecha);
                echo json_encode([
                    'pedidos'=>$pedidos,
                    'exito'=>'pedidos traidos con exito'
                ]);
                exit;
            }
            
            //Si tiene los dos parametos, traemos los pedidos filtrados por ambos parametros
            $pedidos = PedidosAdmin::where_filtrados($fecha, $estado);

            echo json_encode([
                'pedidos'=>$pedidos,
                'exito'=>'pedidos traidos con exito'
            ]);
            exit;
        }
    }
    public static function get_pedidos_where_actualizado(){
        verificar_admin();
        if($_SERVER['REQUEST_METHOD']==='GET'){
            

            $columna = $_GET['c'];
            $valor = $_GET['v'];
            if(!$columna || !$valor){
                imprimirRespuestaJson('error','columna o valor invalida');
            }

            $pedidos = PedidosAdmin::where($columna, $valor);

            echo json_encode([
                'pedidos'=>$pedidos,
                'exito'=>'pedidos traidos con exito'
            ]);
            exit;
        }
        
    }
    public static function actualizar_estado_pedido(){
            verificar_admin();
        if($_SERVER['REQUEST_METHOD']==='POST'){
            
            if(!is_numeric($_POST['id']) || !$_POST['id']){
                echo json_encode(['error'=>'id invalido']);
                exit;
            }
            $pedido = Pedidos::where('id',$_POST['id']);
            //cambiarlo con un if
            if($_POST['status'] !== 'pendiente' && $_POST['status'] !== 'completado'){
                echo json_encode(['error'=>'estado invalido']);
                exit;
            }
            $pedido->status = ($_POST['status'] === 'pendiente')? 'completado' : 'pendiente';
            $resultado = $pedido->guardar();
            if(!$resultado){
                echo json_encode(['error'=>'no se pudo actualizar']);
                exit; 
            }
            //verificar fecha y estado para retornar los productos filtrados.
            /* $pedidos = PedidosAdmin::all(); */
            $resultado = PedidosAdmin::verificar_filtrar_fecha_and_estado($_POST['fecha'],$_POST['estado']);
            echo json_encode($resultado);
            exit;
            /* echo json_encode([
                'pedidos'=>$pedidos,
                'exito'=>'Se actualizo correctamente'
            ]);
            exit; */
            

        }
    }
    //Landing Pages.
    public static function formulario_landing_page(){

        if($_SERVER['REQUEST_METHOD']=== 'POST'){
            $data = file_get_contents('php://input');
            $datos = json_decode($data,true);

            $form = new FormularioLandingPage($datos);
            $form->validar_campos_obligatorios();
            $form->validar_otro_cuadro();
            $alertas = FormularioLandingPage::getAlertas();
            if(!empty($alertas)){
                http_response_code(400);
                imprimirRespuestaJson('error', $alertas['error']);
            }
            
            //Paso la validacion...
            //enviar email
            $mail = new Email($form->correo,$form->nombre);
            $array = get_object_vars($form);
            $resultado = $mail->enviar_formulario_landingPage($array);
            
            if($resultado){
                
                echo json_encode([
                    'exito'=>'Correo enviado exitosamente'
                ]);
                return;
            }
            http_response_code(500);
            imprimirRespuestaJson('error', $resultado);

            
        }

    }
    public static function cuenta_regresiva(){

        if($_SERVER['REQUEST_METHOD']==='GET'){
            date_default_timezone_set('America/Bogota');

            if(!isset($_SESSION['cuenta_regresiva'])){
                //asignar cuenta regresiva.
                $fecha_objetivo = new DateTime('now');
                $fecha_objetivo->modify('+5 hour');
                $_SESSION['cuenta_regresiva']['fecha_inicio'] = $fecha_objetivo;
            }
            
            $fecha_objetivo = $_SESSION['cuenta_regresiva']['fecha_inicio'];
            $fecha_actual = new DateTime('now');
            $diferencia = $fecha_objetivo->diff($fecha_actual); 

            $totalSegundos = $diferencia->s + ($diferencia->i * 60) + ($diferencia->h * 3600);
            if($totalSegundos <= 0){
                //ya llego a 0 el contador.
                //volvemos a reescribir la session.
                $fecha_objetivo = new DateTime('now');
                $fecha_objetivo->modify('+5 hour');
                $_SESSION['cuenta_regresiva']['fecha_inicio'] = $fecha_objetivo;
                $fecha_objetivo = $_SESSION['cuenta_regresiva']['fecha_inicio'];
                $fecha_actual = new DateTime('now');
                $diferencia = $fecha_objetivo->diff($fecha_actual);

            }

            $cuenta_regresiva = [
                'segundos'=> $diferencia->s,
                'minutos'=> $diferencia->i,
                'horas'=> $diferencia->h + ($diferencia->days * 24)

            ];
            
            echo json_encode([
                'respuesta'=>true,
                'cuenta_regresiva'=>$cuenta_regresiva
            ]);
            exit;
        }

    }
}