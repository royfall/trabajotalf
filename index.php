<?php
// constantes base
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', realpath(__DIR__) . DS);
define('APP_PATH', BASE_PATH . 'protected' . DS);

// configuración base
$cfg = require APP_PATH . 'config.php'; 

// funciones base
require APP_PATH . 'functions.php';

// autoload
spl_autoload_register('class_autoload');

// dispatch
dispatch(get_url_segments());