<?php 

namespace Model;

class ProductosComprados extends ActiveRecord{
    protected $columndasDB = ['nombre','cantidad'];

    public $nombre;
    public $cantidad;

    public function __construct($args = [])
    {
        $this->nombre = $args['nombre'] ?? '';
        $this->cantidad = $args['cantidad'] ?? NULL;
    }
}