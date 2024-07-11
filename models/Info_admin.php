<?php

namespace Model;

class Info_admin extends ActiveRecord{

    protected static $columnasDB = ['cantidad_pedidos', 'cantidad_productos', 'cantidad_usuarios'];
    public $cantidad_pedidos;
    public $cantidad_productos;
    public $cantidad_usuarios;

    public function __construct($args = [])
    {
        $this->cantidad_pedidos = $args['cantidad_pedidos'] ?? '';
        $this->cantidad_productos = $args['cantidad_productos'] ?? '';
        $this->cantidad_usuarios = $args['cantidad_usuarios'] ?? '';
        
    }

}