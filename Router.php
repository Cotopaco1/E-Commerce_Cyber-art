<?php

namespace MVC;

class Router
{
    public array $getRoutes = [];
    public array $postRoutes = [];

    public function get($url, $fn)
    {
        $this->getRoutes[$url] = $fn;
    }

    public function post($url, $fn)
    {
        $this->postRoutes[$url] = $fn;
    }

    public function comprobarRutas()
    {
        
        // Proteger Rutas...
        session_start();
       /*   */
        // Arreglo de rutas protegidas...
        // $rutas_protegidas = ['/admin', '/propiedades/crear', '/propiedades/actualizar', '/propiedades/eliminar', '/vendedores/crear', '/vendedores/actualizar', '/vendedores/eliminar'];

        // $auth = $_SESSION['login'] ?? null;
        $rutas_login = ['/crear_cuenta', '/recuperar_password', '/reestablecer_password'];
        $currentUrl = $_SERVER['PATH_INFO'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        
        //Si estas entrando a admin, entonces debes ser admin
        /* $resultado_admin = strpos($currentUrl, '/admin') !== false;
        if($resultado_admin){
            if(!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
                header('location: /');
            }
        } */
        
        //Si estas entrando a una ruta login que requiera no estar iniciado con sesion entonces...
        if(in_array($currentUrl, $rutas_login)){
            $auth = $_SESSION['login'] ?? false ;
            if($auth){
                header('location: /');
            }
        }

        if ($method === 'GET') {
            $fn = $this->getRoutes[$currentUrl] ?? null;
        } else {
            $fn = $this->postRoutes[$currentUrl] ?? null;
        }


        if ( $fn ) {
            // Call user fn va a llamar una función cuando no sabemos cual sera
            call_user_func($fn, $this); // This es para pasar argumentos
        } else {
            echo "Página No Encontrada o Ruta no válida";
        }
    }

    public function render($view, $datos = [], $layout = 'layout')
    {

        // Leer lo que le pasamos  a la vista
        foreach ($datos as $key => $value) {
            $$key = $value;  // Doble signo de dolar significa: variable variable, básicamente nuestra variable sigue siendo la original, pero al asignarla a otra no la reescribe, mantiene su valor, de esta forma el nombre de la variable se asigna dinamicamente
        }

        ob_start(); // Almacenamiento en memoria durante un momento...

        // entonces incluimos la vista en el layout
        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // Limpia el Buffer
        include_once __DIR__ . "/views/$layout.php";
    }
}
