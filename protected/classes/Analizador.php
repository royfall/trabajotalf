<?php

class Analizador {
    private $automata;
    
    function __construct($automata)
    {
        $this->automata = $automata;
    }
    
    function evaluarPalabra($palabra = '')
    {
        $largo = strlen($palabra);
        $q = $this->automata->getEstados();
        if(empty($q))
			return;
        $simbolos = $this->automata->getSimbolos();
        $i = 0;
        $estado = array_shift($q);

        if(empty($palabra[$i])) {
            if($estado->esFinal())
                 return true;    
                
             return false;
        }
        
        while($i < $largo) {
            $caracter = $palabra[$i];

            if(!in_array($caracter, $simbolos))
                return false;
            
            $transiciones = $estado->getTransiciones($caracter);
            $trans = $transiciones[0];
            
            if(!is_numeric($trans))
                return false;
            
            $estado = $this->automata->getEstadoByID($trans);
            
            if(!isset($palabra[$i+1])) {
                if($estado->esFinal())
                    return true;
                
                return false;
            }

            $i++;
        }

        return true;
    }
    
    function generarPalabrasN($n=0, $k=0)
    {
        $palabras = array();
        $palabras_validas = array();
        $cant_palabras = 1;

        $simbolos = $this->automata->getSimbolos();
        
        for($i = $k; $i > 0; $i--) {
            $salida = '';
            ob_start();
            combina($simbolos, '', $i, 0);
            $salida = ob_get_clean();
            $salida = explode("\n", $salida);
            array_pop($salida);
            $palabras = array_merge($palabras, $salida);
        }
        
        $palabras[] = '';
        
        // evaluar palabras
        foreach($palabras as $palabra) {
            if($this->evaluarPalabra($palabra)) {
                $palabras_validas[] = $palabra;
                $cant_palabras++;
            }
            
            if($cant_palabras > $n)
                break;
        }
        
        return $palabras_validas;
    }
}
