<?php

namespace Model;

class PedidosAdmin extends ActiveRecord{

    protected static $columnasDB = ['id', 'fecha', 'status', 'total', 'nombre','cedula','email','telefono', 'direccion', 'metodo_pago', 'informacion_adicional', 'productoId','productoNombre','productoCantidad'];

    public $id;
    public $fecha;
    public $status;
    public $total;
    public $nombre;
    public $cedula;
    public $email;
    public $telefono;
    public $direccion;
    public $metodo_pago;
    public $informacion_adicional;
    public $productoId;
    public $productoNombre;
    public $productoCantidad;

   /*  public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->fecha = $args['fecha'] ?? null;
        $this->status = $args['status'] ?? '';
        $this->total = $args['total'] ?? null;
        $this->nombre = $args['nombre'] ?? null;
        $this->email = $args['email'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->metodo_pago = $args['metodo_pago'] ?? '';
        $this->informacion_adicional = $args['informacion_adicional'] ?? 0;
        $this->productoId = $args['productoId'] ?? '';
    } */
    public function validar(){

        if(!$this->fecha){
            self::setAlerta('error', 'El nombre es obligatorio');
        }
        if(!$this->status){
            self::setAlerta('error', 'El status hace falta');
        }
        if(!$this->monto_total){
            self::setAlerta('error', 'El email es obligatorio');
        }
        if(!$this->direccion){
            self::setAlerta('error', 'La direccion es obligatoria');
        }
        if(!$this->departamento){
            self::setAlerta('error', 'El departamento es obligatorio');
        }
        
        if(!$this->ciudad){
            self::setAlerta('error', 'La ciudad es obligatoria');
        }
        if(!$this->metodo_pago){
            self::setAlerta('error', 'El metodo de pago es obligatorio');
        }

        return self::$alertas;
    }
    public function validar_direccion(){

        if(!$this->direccion){
            self::setAlerta('error', 'La direccion es obligatoria');
        }
        if(!$this->departamento){
            self::setAlerta('error', 'El departamento es obligatorio');
        }
        
        if(!$this->ciudad){
            self::setAlerta('error', 'La ciudad es obligatoria');
        } 

        return self::$alertas;
    }
    public static function all(){
        $query = "SELECT pedidos.id,pedidos.fecha,pedidos.status,pedidos.monto_total as total,";
        $query .= "CONCAT(usuarios.nombre,' ', usuarios.apellido) as nombre,";
        $query .= "usuarios.cedula,usuarios.email, usuarios.telefono, ";
        $query .= "CONCAT(pedidos.direccion, ' ', pedidos.departamento, ' ', pedidos.ciudad) as direccion,";
        $query .= "pedidos.metodo_pago,pedidos.informacion_adicional, items_pedido.productosId as productoId, productos.nombre as productoNombre, items_pedido.cantidad as productoCantidad ";
        $query .= "FROM pedidos INNER JOIN usuarios ON pedidos.usuarioID=usuarios.id ";
        $query .= "INNER JOIN items_pedido ON items_pedido.pedidosId=pedidos.id ";
        $query .= "INNER JOIN productos ON items_pedido.productosId=productos.id";
            
        $pedidos = self::SQL($query);
        return $pedidos;
    }
    public static function where($columna,$valor){
        $query = "SELECT pedidos.id,pedidos.fecha,pedidos.status,pedidos.monto_total as total,";
        $query .= "CONCAT(usuarios.nombre,' ', usuarios.apellido) as nombre,";
        $query .= "usuarios.cedula,usuarios.email, usuarios.telefono, ";
        $query .= "CONCAT(pedidos.direccion, ' ', pedidos.departamento, ' ', pedidos.ciudad) as direccion,";
        $query .= "pedidos.metodo_pago,pedidos.informacion_adicional, items_pedido.productosId as productoId, productos.nombre as productoNombre, items_pedido.cantidad as productoCantidad ";
        $query .= "FROM pedidos INNER JOIN usuarios ON pedidos.usuarioID=usuarios.id ";
        $query .= "INNER JOIN items_pedido ON items_pedido.pedidosId=pedidos.id ";
        $query .= "INNER JOIN productos ON items_pedido.productosId=productos.id ";   
        $query .= "WHERE $columna = '$valor'";
        // $pedidos = PedidosAdmin::SQL($query);
        /* debugear($query); */
        $pedidos = self::SQL($query);
        return $pedidos;
    }
    public static function where_filtrados($fecha,$estado){
        $query = "SELECT pedidos.id,pedidos.fecha,pedidos.status,pedidos.monto_total as total,";
        $query .= "CONCAT(usuarios.nombre,' ', usuarios.apellido) as nombre,";
        $query .= "usuarios.cedula,usuarios.email, usuarios.telefono, ";
        $query .= "CONCAT(pedidos.direccion, ' ', pedidos.departamento, ' ', pedidos.ciudad) as direccion,";
        $query .= "pedidos.metodo_pago,pedidos.informacion_adicional, items_pedido.productosId as productoId, productos.nombre as productoNombre, items_pedido.cantidad as productoCantidad ";
        $query .= "FROM pedidos INNER JOIN usuarios ON pedidos.usuarioID=usuarios.id ";
        $query .= "INNER JOIN items_pedido ON items_pedido.pedidosId=pedidos.id ";
        $query .= "INNER JOIN productos ON items_pedido.productosId=productos.id ";
        $query .= "WHERE fecha = '$fecha' AND status = '$estado'";
        // $pedidos = PedidosAdmin::SQL($query);
        $pedidos = self::SQL($query);
        return $pedidos;
    }
    public static function verificar_filtrar_fecha_and_estado($fecha,$estado){
        if(!$fecha && !$estado){
            imprimirRespuestaJson('error','parametros invalidos');
            exit;
        }
        if(!$fecha){
            //si no existe fecha, traemos filtrado por estado.
            if($estado === 'todos'){
                //si estado es todos y fecha no existe traemos
                //traemos todos los pedidos.
                $pedidos = self::all();
                $resultado = [
                    'pedidos'=>$pedidos,
                    'exito'=>'pedidos traidos con exito'
                ];
            return $resultado;
            }
            $pedidos = self::where('status',$estado);
            $resultado = [
                'pedidos'=>$pedidos,
                'exito'=>'pedidos traidos con exito'
            ];
            return $resultado;
        }
        //si estado es todos o no existe buscamos solo por fecha..
        if(!$estado || $estado === 'todos'){
            //si no existe estado entonces traemos filtrado por fecha
            $pedidos = PedidosAdmin::where('fecha',$fecha);
            $resultado = [
                'pedidos'=>$pedidos,
                'exito'=>'pedidos traidos con exito'
            ];
            return $resultado;
        }
        //Si tiene los dos parametos, traemos los pedidos filtrados por ambos parametros
        $pedidos = self::where_filtrados($fecha, $estado);
        
        $resultado = [
            'pedidos'=>$pedidos,
            'exito'=>'pedidos traidos con exito'
        ];
        return $resultado;

    }
}