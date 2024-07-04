<?php

namespace Model;

class Usuarios extends ActiveRecord{
    
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin =  0;
        $this->confirmado = 0;
        $this->token = '';
    }

    public function validar(){

        if(!$this->nombre){
            self::setAlerta('error', 'El nombre es obligatorio');
        }
        if(!$this->apellido){
            self::setAlerta('error', 'El apellido es obligatorio');
        }
        if(!$this->email){
            self::setAlerta('error', 'El email es obligatorio');
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL) && $this->email){
            self::setAlerta('error', 'El email no es valido');
        }
        if(!$this->telefono){
            self::setAlerta('error', 'El telefono es obligatorio');
        }
        $numero_permitido_de_celular = 10;
        if(strlen($this->telefono) !== $numero_permitido_de_celular && $this->telefono){
            self::setAlerta('error', 'El telefono debe ser de 10 numeros');
        }

        return self::$alertas;
    }
   
}