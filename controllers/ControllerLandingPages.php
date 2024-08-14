<?php

namespace Controller;

use MVC\Router;

class ControllerLandingPages{
    public static function rickAndMorty(Router $router){
        $router->render('landingPages/rickAndMorty', [], 'landingPages/layout');
    }
}