<?php

namespace Model;

class PedidosAdmin extends ActiveRecord{

    protected static $columnasDB = ['id', 'fecha', 'status', 'total', 'nombre','email','telefono', 'direccion', 'metodo_pago', 'informacion_adicional', 'productoId'];

    public $id;
    public $fecha;
    public $status;
    public $total;
    public $nombre;
    public $email;
    public $telefono;
    public $direccion;
    public $metodo_pago;
    public $informacion_adicional;
    public $productoId;

    public function __construct($args = [])
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
    }
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
   
}