<?php
/**
 * Realiza la carga automática de clases
 *
 * @param string $class_name nombre de la clase
 * @return void
 */
function class_autoload($class_name)
{
    $file_path = APP_PATH . 'classes' . DS . $class_name . '.php';
    
    if(file_exists($file_path))
	require $file_path;
}

/**
 * Retorna los segmentos de la URL
 *
 * @return array con segmentos de URL
 */
function get_url_segments()
{
    $request_uri = explode('/', $_SERVER['REQUEST_URI']);
    $script_name = explode('/', $_SERVER['SCRIPT_NAME']);
    $script_name_size = sizeof($script_name) - 1;
    
    for($i = 0; $i < $script_name_size; $i++)
        unset($request_uri[$i]);
    
    if($request_uri[$i] == 'index.php')
	unset($request_uri[$i]);

    return array_values($request_uri);
}

/**
 * Envía al controlador / acción segun los segmentos ingresados
 *
 * @param array $segments segmentos de URL
 *
 */
function dispatch($segments = array())
{
    (!empty($segments[0])) ? $controller = strtolower(array_shift($segments)) : $controller = $GLOBALS['cfg']['default_controller'];
    (!empty($segments[0])) ? $action = strtolower(array_shift($segments)) : $action = 'index';
    $GLOBALS['action'] = $action;
    $controller_path = BASE_PATH . 'protected' . DS . 'controllers' . DS . $controller . '_controller.php';
    $controller_name = $controller;
    $controller = ucfirst($controller) . 'Controller';
    
    if(file_exists($controller_path))
	require $controller_path;
    else
	die('Error, controller <b>' . $controller . '</b> could not be found');
    
    if(class_exists($controller))
	$new_controller = new $controller($controller_name);
    else
	die('Error, controller class <b>' . $controller . '</b> could not be found');

    $new_controller->_before_action();

    if(is_callable(array($new_controller, $action)) && strncmp("_", $action, 1) != 0)
	call_user_func_array(array($new_controller, $action), $segments);
    else
	die('Error, action <b>' . $action . '</b> could not be found');
	
    $new_controller->_after_action();
    $new_controller->_render();
}

/**
 * Imprime un arreglo
 *
 * @param array $arr arreglo
 */
function pr($arr = null)
{
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

/**
 * Genera combinatoria para simbolos de manera recursiva
 *
 * @param array $simbolos arreglo con simbolos de entrada
 * @param string $combinacion la cadena correspondiente a la combinación
 * @param integer $k largo de la combinación
 * @param integer $j contador
 */
function combina($simbolos, $combinacion, $k, $j)
{
    $total_simbolos = count($simbolos);

    if(strlen($combinacion) == $k)  {
        echo $combinacion . "\n";  
    }
    
    if($j < $k) {
        ++$j;

        for($i = 0; $i < $total_simbolos; $i++) {
                combina($simbolos, $combinacion . $simbolos[$i], $k, $j);
        }
    }
}

/**
 * Compara 2 elementos, retorna 0 si son iguales, retorna -1 si $a < que $b y 1 si $a > $b
 *
 * @param mixed $a primer elemento
 * @param mixed $b segundo elemento
 *
 * @return integer
 */
function cmp($a, $b)
{
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
}
