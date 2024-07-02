<?php

namespace Model;

class Pedidos extends ActiveRecord{
    
    protected static $tabla = 'pedidos';
    protected static $columnasDB = ['id', 'usuarioID', 'fecha', 'status', 'monto_total', 'direccion', 'metodo_pago', 'coste_envio', 'informacion_adicional'];

    public $id;
    public $usuarioID;
    public $fecha;
    public $status;
    public $monto_total;
    public $direccion;
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
        $this->metodo_pago = $args['metodo_pago'] ?? '';
        $this->coste_envio = $args['coste_envio'] ?? 0;
        $this->informacion_adicional = $args['informacion_adicional'] ?? '';
    }
   /*  // crea un nuevo registro
    public function crear() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' "; 
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";

        // Resultado de la consulta
        $resultado = self::$db->query($query);
        return [
           'resultado' =>  $resultado,
           'id' => self::$db->insert_id
           'pedidoId'=> $this->
        ];
    } */
}