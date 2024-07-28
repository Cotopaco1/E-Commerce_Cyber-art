<?php

namespace Model;

class FormularioPersonalizado extends ActiveRecord{


    public $nombre;
    public $apellido;
    public $contacto;
    public $telefono;
    public $email;
    public $mensaje;

    public function __construct($args = [])
    {
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->contacto = $args['contacto'] ?? '';
        $this->nombre = $args['nombre'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->mensaje = $args['mensaje'] ?? '';
    }

    public function validar(){
        if(!$this->nombre){
            self::setAlerta('error','El nombre es obligatorio');
        }
        if(!$this->apellido){
            self::setAlerta('error','El apellido es obligatorio');
        }
        if(!$this->contacto){
            self::setAlerta('error','El contacto es obligatorio');
        }
        if($this->contacto === 'email' && !$this->email){
            self::setAlerta('error','El email es obligatorio');
        }
        if($this->contacto === 'telefono' && !$this->telefono){
            self::setAlerta('error','El telefono es obligatorio');
        }
        return self::$alertas;
        
    }
    public function validar_email(){
        $esValido = filter_var($this->email, FILTER_VALIDATE_EMAIL );
        if(!$esValido){
            self::setAlerta('error','El email no es valido');
        }
        return self::$alertas;
    }
}