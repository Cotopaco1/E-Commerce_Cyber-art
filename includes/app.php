<?php

use Model\ActiveRecord;

require 'database.php';
include 'funciones.php';
include __DIR__ . '/../vendor/autoload.php';

//Seteo base de datos en la clase padre.
ActiveRecord::setDB($db);
