<?php

use Dotenv\Dotenv;
use Model\ActiveRecord;
include __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require 'database.php';
include 'funciones.php';


//Seteo base de datos en la clase padre.
ActiveRecord::setDB($db);
