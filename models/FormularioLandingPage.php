<?php

namespace Model;

class FormularioLandingPage extends ActiveRecord{
    
    public $nombre;
    public $correo;
    public $cedula;
    public $telefono;
    public $departamento;
    public $ciudad;
    public $direccion;
    public $metodoPago;
    public $oferta;
    public $otro_cuadro;
    public $otro_producto_nombre;

    public function __construct($args = [])
    {
        $this->nombre = $args['nombre'] ?? '';
        $this->correo = $args['correo'] ?? '';
        $this->cedula = $args['cedula'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->departamento = $args['departamento'] ?? '';
        $this->ciudad = $args['ciudad'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->metodoPago = $args['metodoPago'] ?? '';
        $this->oferta = $args['oferta'] ?? '';
        $this->otro_cuadro = $args['otro_cuadro'] ?? NULL;
        $this->otro_producto_nombre = $args['otro_producto_nombre'] ?? '';
    }

    public function validar_campos_obligatorios(){
        if(!$this->nombre){
            self::setAlerta('error', 'nombre');
        }
        if(!$this->correo){
            self::setAlerta('error', 'correo');
        }
        if(!$this->cedula){
            self::setAlerta('error', 'cedula');
        }
        if(!$this->telefono){
            self::setAlerta('error', 'telefono');
        }
        if(!$this->departamento){
            self::setAlerta('error', 'departamento');
        }
        if(!$this->ciudad){
            self::setAlerta('error', 'ciudad');
        }
        if(!$this->direccion){
            self::setAlerta('error', 'direccion');
        }
        if(!$this->metodoPago){
            self::setAlerta('error', 'metodoPago');
        }
        if(!$this->oferta){
            self::setAlerta('error', 'oferta');
        }
        return self::$alertas;
    }
    public function validar_otro_cuadro(){
        if($this->otro_cuadro && !$this->otro_producto_nombre){
            //error
            self::setAlerta('error','slider-manual');
            return;
        }
        if($this->otro_cuadro && $this->otro_producto_nombre || !$this->otro_cuadro && !$this->otro_producto_nombre){
            //exitoso, retornamos sin alertas.
            return;
        }
        self::setAlerta('error', 'Hubo un error..');
        return self::$alertas;

    }
}