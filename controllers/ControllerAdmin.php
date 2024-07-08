<?php

namespace Controller;

use MVC\Router;

class ControllerAdmin{

    public static function index(Router $router){


        $router->render('admin/index', [], 'admin/layout');
    }
}