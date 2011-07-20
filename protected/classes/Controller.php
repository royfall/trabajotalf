<?php
abstract class Controller {
    protected $vars = array();
    protected $layout;
    protected $controller_name;
    
    function __construct($controller_name)
    {
	$this->controller_name = $controller_name;
    }
    
    function __set($name, $value)
    {
         $this->vars[$name] = $value;
    }
    
    function __get($name)
    {
        return $this->vars[$name];
    }
    
    function _set_vars($vars, $value)
    {
        if(!is_array($vars))
            $this->vars[$vars] = $value;
	else
            $this->vars += $vars;
    }
    
    function _before_action()
    {}
    
    function _after_action()
    {}
    
    function _render()
    {
	global $cfg;
        extract($this->vars);
        // load view
        
        // load layout
        if(empty($this->layout))
            $this->layout = $GLOBALS['cfg']['default_layout'];
        
        $view_name = strtolower($GLOBALS['action']);
        $view_path = APP_PATH . DS . 'views' . DS . $this->controller_name . DS . $view_name . '.phtml';
        ob_start();
        if (file_exists($view_path))
            require $view_path;
        $view_contents = ob_get_clean();
        
        $layout_path = APP_PATH . DS . 'views' . DS . $this->layout . '.phtml';
        
        if(file_exists($layout_path))
            require $layout_path;
        else
            die('Error, layout file <pre>' . $layout_path . '</pre> could not be found');
    }
}
