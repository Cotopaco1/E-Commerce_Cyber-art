<?php

namespace Model;

class Pedidos extends ActiveRecord{
    
    protected static $tabla = 'pedidos';
    protected static $columnasDB = ['id', 'usuarioID', 'fecha', 'status', 'monto_total', 'direccion','departamento','ciudad', 'metodo_pago', 'coste_envio', 'informacion_adicional'];

    public $id;
    public $usuarioID;
    public $fecha;
    public $status;
    public $monto_total;
    public $direccion;
    public $departamento;
    public $ciudad;
    public $metodo_pago;
    public $coste_envio;
    public $informacion_adicional;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->usuarioID = $args['usuarioID'] ?? null;
        $this->fecha = $args['fecha'] ?? '';
        $this->status = $args['status'] ?? null;
        $this->monto_total = $args['monto_total'] ?? null;
        $this->direccion = $args['direccion'] ?? '';
        $this->departamento = $args['departamento'] ?? '';
        $this->ciudad = $args['ciudad'] ?? '';
        $this->metodo_pago = $args['metodo_pago'] ?? '';
        $this->coste_envio = $args['coste_envio'] ?? 0;
        $this->informacion_adicional = $args['informacion_adicional'] ?? '';
    }
    public function validar(){

        if(!$this->fecha){
            self::setAlerta('error', 'El nombre es obligatorio');
        }
        if(!$this->status){
            self::setAlerta('error', 'El status hace falta');
        }
        if(!$this->monto_total){
            self::setAlerta('error', 'El monto_total es obligatorio');
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