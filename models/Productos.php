<?php

namespace Model;

class Productos extends ActiveRecord{

    protected static $tabla = 'productos';
    protected static $columnasDB = ['id', 'nombre', 'precio', 'descripcion', 'size', 'disponible', 'imagen'];

    public $id;
    public $nombre;
    public $precio;
    public $descripcion;
    public $size;
    public $disponible;
    public $imagen;

    public  function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->precio = $args['precio'] ?? null;
        $this->descripcion = $args['descripcion'] ?? '';
        $this->size = $args['size'] ?? '';
        $this->disponible = $args['disponible'] ?? null;
        $this->imagen = $args['imagen'] ?? '';
    }

    public function setImagen($nombreImagen){

        // $imagen = CARPETA_IMAGENES_PRODUCTOS . "/$nombreImagen";
        $imagen = CARPETA_IMAGENES_PRODUCTOS . "/$this->imagen";
        
        if(!$this->imagen || !file_exists($imagen)){
            $this->imagen = $nombreImagen;
            return;
        }
        if($this->borrarImagen()){

            $this->imagen = $nombreImagen;
            return true;
        
        }
        return false;
        //borar imagen anterior.

    }
    public function borrarImagen(){
        $imagen = CARPETA_IMAGENES_PRODUCTOS . "/$this->imagen";

        if(file_exists($imagen)){
            $resultado = unlink($imagen);
            if($resultado){
                return true;
            }
            
        }
        return false;

    }
    public function validar(){
        if(!$this->nombre){
            self::setAlerta('error','El nombre es requisito');
        }
        if(!$this->precio){
            self::setAlerta('error','El precio es requisito');
        }
        if(!$this->descripcion){
            self::setAlerta('error','la descripcion es requisito');
        }
        if(!$this->size){
            self::setAlerta('error','El tamaño es requisito');
        }
        if($this->disponible === ''){
            self::setAlerta('error','La disponibilidad es requisito');
        }
        if(!$this->imagen){
            self::setAlerta('error','La imagen es requisito');
        }
        return self::$alertas;
    }
}