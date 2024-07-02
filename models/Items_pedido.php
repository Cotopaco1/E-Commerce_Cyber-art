<?php

namespace Model;

class Items_pedido extends ActiveRecord{
    protected static $tabla = 'items_pedido';
    protected static $columnasDB = ['id', 'pedidosId', 'productosId', 'cantidad', 'precio', 'precio_total'];

    public $id;
    public $pedidosId;
    public $productosId;
    public $cantidad;
    public $precio;
    public $precio_total;
    

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->pedidosId = $args['pedidosId'] ?? null;
        $this->productosId = $args['productosId'] ?? null;
        $this->cantidad = $args['cantidad'] ?? null;
        $this->precio = $args['precio'] ?? null;
        $this->precio_total = $args['precio_total'] ?? null;
        
    }
    
}