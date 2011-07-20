<?php

class Estado {
    private $id;
    private $estado_inicial;
    private $estado_final;
    private $transiciones;
    private $estados_AFND;
    
    function __construct($id, $inicial = false, $final = false, $estados_AFND = array())
    {
        $this->id = $id;
        $this->estado_inicial = $inicial;
        $this->estado_final = $final;
        $this->estados_AFND = $estados_AFND;
    }
    
    function getID()
    {
        return $this->id;
    }
    
    function setID($id)
    {
        return $this->id = $id;
    }
    
    function esInicial()
    {
        return $this->estado_inicial;
    }
    
    function esFinal()
    {
        return $this->estado_final;
    }
    
    function setInicial($inicial = false)
    {
        $this->estado_inicial = $inicial;
    }
    
    function setFinal($final = false)
    {
        $this->estado_final = $final;
    }
    
    function setTransicion($simbolo, $estado)
    {
        if(is_object($estado))
            $this->transiciones[$simbolo][] = $estado->getID();
        else
            $this->transiciones[$simbolo][] = $estado;
    }
    
    function setTransiciones($transiciones)
    {
        $this->transiciones = $transiciones;
    }
    
    function getTransiciones($simbolo)
    {
        if(isset($this->transiciones[$simbolo]) && !empty($this->transiciones[$simbolo]))
            return $this->transiciones[$simbolo];
        elseif(empty($this->transiciones[$simbolo]))
            return array('Ф');
        else
            return array('Ф');
    }
    
    function getArregloTransiciones()
    {
        return $this->transiciones;
    }
    
    function agregarConjunto($conjunto = array())
    {
        $this->estados_AFND = $conjunto;
		$this->estado_final = false;
		
		// si el conjunto contiene un estado final, entonces este estado tambien es final
		foreach($this->getEstadosAFND() as $estado) {
            if($estado->esFinal()) {
                $this->estado_final = true;
                return;
            }
        }
    }
    
    function esEstadoBasura()
    {
		// si es final, no es estado basura
		if($this->esFinal())
			return false;
		
		// si una de las transiciones no va al mismo estado, entonces no es basura
        foreach($this->transiciones as $simbolo => $valor) {
            if($valor[0] != $this->getID())
                return false;
        }
        
        return true;
    }
    
    function eliminaTransicion($estado)
    {
        foreach($this->transiciones as $simbolo => $valor) {
            if($valor[0] == $estado->getID())
                array_pop($this->transiciones[$simbolo]);
        }
    }
    
    function getEstadosAFND()
    {
        return $this->estados_AFND;
    }
}
