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

    }

    public function validar_datos_para_formulario_crear_pedido(){

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
   
    public function validarNuevaCuenta(){

        $minCharactersPassword = 6;

        if(!$this->nombre){
            /* self::$alertas['error'][] = 'El nombre es requisito'; */
            self::setAlerta('error', 'El nombre es obligatorio...');
        }
        if(!$this->apellido){
            self::$alertas['error'][] = 'El apellido es requisito';
        }
        /* if(!$this->telefono){
            self::$alertas['error'][] = 'El telefono es requisito';
        } */
        if(!$this->email){
            self::$alertas['error'][] = 'El email es requisito';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password es requisito';
        }
        if(strlen($this->password)< $minCharactersPassword && $this->password){
            self::$alertas['error'][] = "El password debe tener minimo {$minCharactersPassword} caracteres";
        }
       
        return self::$alertas;
    }
    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'La password es obligatoria';
        }
        return self::$alertas;
    }
    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] =  'El email es obligatorio';
        }

    }
    public function validarPassword(){
        $minCharactersPassword = 6;
        if(!$this->password){
            self::$alertas['error'][] = 'La password es obligatoria';
        }
        if(strlen($this->password) < $minCharactersPassword && $this->password){
            self::$alertas['error'][] = "La password debe contener minimo {$minCharactersPassword} caracteres";
        }
        return self::$alertas;
    }

    public function userExist(){
        $sanitizado = $this->sanitizarAtributos();
        $email = $sanitizado['email'];
        $query = "SELECT * FROM ";
        $query .= self::$tabla;
        $query .= " WHERE email = " . "'$email'";

        $resultado = self::$db->query($query);

        return $resultado;
    }

    public function hashearPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }
    public function crearToken(){
        $this->token = uniqid();
    }

    public static function confirmarUsuario_con_sentencia_preparada($token){
        $resultado = self::where_con_sentencia_preparada('token', $token);
        if($resultado->num_rows > 0){
            $usuario = new Usuarios($resultado->fetch_assoc());
            $usuario->token = '';
            $usuario->confirmado = 1;
            $usuario->guardar();
            self::setAlerta('exito', 'El usuario ha sido confirmado con exito');
            return self::$alertas;
        }else{
            self::setAlerta('error', 'Token invalido o la cuenta ya ha sido confirmada');
            return self::$alertas;

        }
    }
    public static function confirmarUsuario($token){
        $user = Usuarios::where('token', $token);
        if($user){
            $user->token = NULL;
            $user->confirmado = 1;
            $user->guardar();
            self::setAlerta('exito', 'Cuenta confirmada');
        }
        else{
            self::setAlerta('error', 'Link invalido o la cuenta ya ha sido confirmada...');
        }
        
    }

    public function comprobarPasswordAndVerificado($passwordPost){

        $resultado = password_verify($passwordPost, $this->password);

        if(!$resultado){
            self::$alertas['error'][] = 'La password es incorrecta';
            return false;
        }
        return true;
  
        /* if(!$resultado || !$this->confirmado){
            self::$alertas['error'][] = 'Tu cuenta no ha sido confirmada o la password es incorrecta';
            return false;
        }else{
            return true;
        } */

    }

    public function comprobar_igualdad_de_password($passwordInput){

        if($this->password !== $passwordInput){
            self::setAlerta('error', 'Las passwords no son iguales...');
            
        }
        return self::$alertas;
    }

    public static function where_con_sentencia_preparada($columna, $valor){

        

        $stmt = self::$db->prepare("SELECT * FROM usuarios WHERE $columna = ? LIMIT 1");
        $stmt->bind_param("s", $valor );
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado;
        /* if($resultado->num_rows === 0){
            return ['resultado'=>'error', 'mensaje'=>'No ha habido resulatdos',
        'debugear'=>$valor,
        'debugearResultado'=> $resultado];
        }else{
            return $resultado->fetch_assoc();
        } */

    }

}