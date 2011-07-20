<?php return array(
    // URL base
    'base_url' => 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['SCRIPT_NAME'], 0, -10),
   // controlador por defecto
    'default_controller' => 'main',
    // layout por defecto
    'default_layout'     => 'layout',
);
