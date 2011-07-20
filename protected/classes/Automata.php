<?php

class Automata {
    private $estados;
    private $simbolos;
    
    function __construct()
    {
        $this->estados = array();
        $this->simbolos = array();
    }
    
    function setEstado($estado)
    {
        $this->estados[] = $estado;
    }
    
    function setEstadoInicio($estado)
    {
        array_unshift($this->estados, $estado);
    }
    
    function setEstados($estados = array())
    {
        $this->estados = $estados;
    }
    
    function setSimbolo($simbolo = "")
    {
        if(!in_array($simbolo, $this->simbolos))
            $this->simbolos[] = $simbolo;
    }
    
    function setSimbolos($simbolos = array())
    {
        $this->simbolos = $simbolos;
        usort($this->simbolos, "cmp");
    }
    
    function getEstados()
    {
        return $this->estados;
    }
    
    function getEstadoByID($id)
    {
        foreach($this->estados as $estado)
            if($estado->getID() == $id)
                return $estado;
    }
    
    function getTotalEstados()
    {
        return count($this->estados);
    }
    
    function getEstado($indice)
    {
        if(!empty($this->estados))
            return $this->estados[$indice];
    }
    
    function getSimbolos()
    {
        return $this->simbolos;
    }
    
    function eliminaEstado($estado)
    {
        $n = count($this->estados);
        for($i = 0; $i < $n; $i++) {
            $estado_actual = $this->estados[$i];
            if($estado_actual->getID() == $estado->getID())
                unset($this->estados[$i]);
        }
    }
    
    function imprimir()
    {
		if(empty($this->estados)) {
			echo "No se pudo mostrar la tabla.";
			return;
		}
		
        $estado_inicial = $this->estados[0];
        $estado_inicial->setInicial(true);
        
        foreach($this->simbolos as $simbolo) {
            echo "\t" . $simbolo;
        }
        echo "\n";
        foreach($this->estados as $estado) {
            echo $estado->getID();
            if($estado->esInicial())
                echo 'i';
            if($estado->esFinal())
                echo 'f';
            foreach($this->simbolos as $simbolo) {
               echo "\t" . implode(',', $estado->getTransiciones($simbolo));
            }
            echo "\n";
        }
    }
}
