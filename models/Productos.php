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
}