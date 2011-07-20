<?php

class MainController extends Controller {
    private $thompson;
    
    function _before_action()
    {
        session_start();
        if(isset($_SESSION['regex']))
            $this->thompson = new Thompson($_SESSION['regex']);
    }
    
    function index()
    {
        if(!empty($_POST['regex']))
            $_SESSION['regex'] = $_POST['regex'];
    }
    
    function verificar_palabra()
    {
        if(!isset($_SESSION['regex']))       
            header("Location: " . $GLOBALS['cfg']['base_url'] . '/');
        
        $this->thompson->generarAFND();
        $this->thompson->AFNDaAFD();
        $AFD = $this->thompson->getAFD();
        
        $analizador = new Analizador($AFD);
        if(isset($_POST['palabra'])) {
            if($_POST['palabra'] == 'ε') $_POST['palabra'] = '';
            $this->vars['resultado'] = $analizador->evaluarPalabra($_POST['palabra']);
        } else
            $this->vars['resultado'] = '';

    }
    
    function generar_palabras()
    {
        if(!isset($_SESSION['regex']))       
            header("Location: " . $GLOBALS['cfg']['base_url'] . '/');
            
        $this->thompson->generarAFND();
        $this->thompson->AFNDaAFD();
        $AFD = $this->thompson->getAFD();
        
        if(!empty($_POST['n']) && !empty($_POST['k'])) {
            $n = $_POST['n'];
            $k = $_POST['k'];
        } else {
            $n = 0;
            $k = 0;
        }
        
        $analizador = new Analizador($AFD);
        $this->vars['palabras'] = $analizador->generarPalabrasN($n, $k);

    }

    function ver_afnd()
    {
        if(!isset($_SESSION['regex']))       
            header("Location: " . $GLOBALS['cfg']['base_url'] . '/');
            
        $this->thompson->generarAFND();
        $AFND = $this->thompson->getAFND();
        
        ob_start();
        echo 'Tabla de transiciones del AFND: ' . "\n";
        $AFND->imprimir();
        $this->vars['tabla'] = ob_get_clean();
        
        $estados = $AFND->getEstados();

    }
    
    function imprimir_afnd()
    {
        $this->layout = 'empty';

        $this->thompson->generarAFND();
        $AFND = $this->thompson->getAFND();

        $estados = $AFND->getEstados();
        $graph = new Image_GraphViz(true, array(), 'AFND', false);
        $graph->setAttributes(array('size' => '8,5', 'rankdir' => 'LR'));
        $transiciones = array();
     
        foreach($estados as $estado) {
            if(!$estado->esFinal()) {
                $graph->addNode('q' . $estado->getID(), array('shape' => 'circle'));
            } else {
                $graph->addNode('q' . $estado->getID(), array('shape' => 'doublecircle'));
            }
            
            $transiciones = $estado->getArregloTransiciones();
            if(!empty($transiciones)) {
                foreach($transiciones as $simbolo => $destinos) {
                    foreach($destinos as $destino)
                        $graph->addEdge(array('q'. $estado->getID() => 'q'.$destino), array('label' => $simbolo));
                }
            }
        }
        
        $graph->image('jpeg');
    }
    
    function ver_afd()
    {
        if(!isset($_SESSION['regex']))       
            header("Location: " . $GLOBALS['cfg']['base_url'] . '/');
            
        $this->thompson->generarAFND();
        $this->thompson->AFNDaAFD();
        $AFD = $this->thompson->getAFD();
        
        ob_start();
        echo 'Tabla de transiciones del AFD: ' . "\n";
        $AFD->imprimir();
        $this->vars['tabla'] = ob_get_clean();
        
        $estados = $AFD->getEstados();
    }
    
    function imprimir_afd()
    {
        $this->layout = 'empty';

        $this->thompson->generarAFND();
        $this->thompson->AFNDaAFD();
        $AFD = $this->thompson->getAFD();

        $estados = $AFD->getEstados();
        $graph = new Image_GraphViz(true, array(), 'AFD', false);
        $graph->setAttributes(array('size' => '8,5', 'rankdir' => 'LR'));
        $transiciones = array();
     
        foreach($estados as $estado) {
            if(!$estado->esFinal()) {
                $graph->addNode('q' . $estado->getID(), array('shape' => 'circle'));
            } else {
                $graph->addNode('q' . $estado->getID(), array('shape' => 'doublecircle'));
            }
            
            $transiciones = $estado->getArregloTransiciones();
            if(!empty($transiciones)) {
                foreach($transiciones as $simbolo => $destinos) {
                    foreach($destinos as $destino)
                        $graph->addEdge(array('q'. $estado->getID() => 'q'.$destino), array('label' => $simbolo));
                }
            }
        }
        
        $graph->image('jpeg');
    }
    
    function descargar_informe()
    {
        $this->layout = 'empty';
    }
    
    function regex_not_found()
    {
        
    }
}
