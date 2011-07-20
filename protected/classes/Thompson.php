<?php

class Thompson {
    private $regex;
    private $siguiente_ID;
    private $AFND;
    private $AFD;
    private $operandos;
    private $operadores;
    private $simbolos;
    
    function __construct($regex)
    {
        $this->regex = $regex;
        $this->siguiente_ID = 1;
        $this->AFND = new Automata();
        $this->AFD = new Automata();
        $this->simbolos[]  = 'ε';
        $this->res = array();
    }
    
    function push($simbolo)
    {
        // crea 2 nuevos estados
        $q0 = new Estado($this->siguiente_ID++);
        $q1 = new Estado($this->siguiente_ID++);
        
        // agrega la transicion de q0 a q1 con el simbolo
        $q0->setTransicion($simbolo, $q1);
        
        // crea un automata parcial con estos 2 estados
        $automata = new Automata();
        $automata->setSimbolo($simbolo);
        $automata->setEstado($q0);
        $automata->setEstado($q1);

        // guarda el automata parcial en el arreglo de operandos
        $this->operandos[] = $automata;
                
        // agrega el simbolo al automata final
        if(!in_array($simbolo, $this->simbolos))
            $this->simbolos[] = $simbolo;
    }
    
    function concat()
    {
        // extrae 2 elementos del stack de operandos
		if(empty($this->operandos))
			return false;
        $B = array_pop($this->operandos);
        
		if(empty($this->operandos))
			return false;
        $A = array_pop($this->operandos);

        // toma el último estado de $A y agrega transicion vacía al primer estado de $B
        $A->getEstado($A->getTotalEstados()-1)->setTransicion('ε', $B->getEstado(0));
        
        // crea un nuevo automata $C que guardará el resultado
        $C = new Automata();
        
        // los estados de $C son los de $A junto con los de $B
        $C->setEstados(array_merge($A->getEstados(), $B->getEstados()));
        
        if(!in_array('ε', $C->getSimbolos()))
           $C->setSimbolo('ε');
        
        // Guarda el resultado en el stack de operandos
        $this->operandos[] = $C;
                
        return true;
    }
    
    function union()
    {
        // extrae 2 elementos del stack de operandos
 		if(empty($this->operandos))
			return false;
        $B = array_pop($this->operandos);
		if(empty($this->operandos))
			return false;
        $A = array_pop($this->operandos);

        // crea 2 nuevos estados
        $q0 = new Estado($this->siguiente_ID++);
        $q1 = new Estado($this->siguiente_ID++);
        
        // al estado q0 se le agregan 2 transiciones vacias a los estados iniciales de $A y $B
        $q0->setTransicion('ε', $A->getEstado(0));
        $q0->setTransicion('ε', $B->getEstado(0));
        
        // Desde el estado final de $A se agrega una transición vacía a q1
        $A->getEstado($A->getTotalEstados()-1)->setTransicion('ε', $q1);
        
        // Desde el estado final de $B se agrega una transición vacía a q1
        $B->getEstado($B->getTotalEstados()-1)->setTransicion('ε', $q1);
        
        // Se agrega en $A el estado q0 al inicio
        $A->setEstadoInicio($q0);

        // Se agrega en $B el estado q1 al final
        $B->setEstado($q1);
        
        // crea un nuevo automata $C que guardará el resultado
        $C = new Automata();
        
        // los estados de $C son los de $A junto con los de $B
        $C->setEstados(array_merge($A->getEstados(), $B->getEstados()));
        
        if(!in_array('ε', $C->getSimbolos()))
           $C->setSimbolo('ε');
        
        // Guarda el resultado en el stack de operandos
        $this->operandos[] = $C;

        return true;
    }
    
    function kleene()
    {
		// Extrae un elemento del arreglo de operandos
		if(empty($this->operandos))
			return false;
        $A = array_pop($this->operandos);
        
        // Crea 2 nuevos estados q0 y q1
        $q0 = new Estado($this->siguiente_ID++);
        $q1 = new Estado($this->siguiente_ID++);
        
        // Desde q0 crea una transición vacia a q1
        $q0->setTransicion('ε', $q1);
        
        // Desde q0 crea una transicion vacia al estado inicial de $A
        $q0->setTransicion('ε', $A->getEstado(0));

		// Crea una transicion vacía desde el estado final de $A a q1
        $A->getEstado($A->getTotalEstados()-1)->setTransicion('ε', $q1);
        
        // Crea una transición vacía desde el estado final de $A al estado inicial de $A
        $A->getEstado($A->getTotalEstados()-1)->setTransicion('ε', $A->getEstado(0));

        // Se agrega en $A el estado q0 al inicio
        $A->setEstadoInicio($q0);
        
        // Se agrega en $A el estado q1 al final
        $A->setEstado($q1);
        
        if(!in_array('ε', $A->getSimbolos()))
           $A->setSimbolo('ε');
        
        // Se guarda el resultado $A en el stack de operandos
        $this->operandos[] = $A;
                
        return true;
    }
    
    function concatExpand($regex)
    {
            $strRes = '';
    
            for($i=0; $i < strlen($regex)-1; ++$i)
            {
                    $cLeft	= $regex[$i];
                    $cRight = $regex[$i+1];
                    $strRes .= $cLeft;
                    if(($this->isInput($cLeft)) || ($this->isRightParanthesis($cLeft)) || ($cLeft == '*'))
                            if(($this->isInput($cRight)) || ($this->isLeftParanthesis($cRight)))
                                    $strRes .= '.';
            }
            $strRes .= $regex[strlen($regex)-1];
    
            return $strRes;
    }
    
    function isInput($char)
    {
        return (!$this->isOperator($char));
    }
    
    function isOperator($char)
    {
        switch($char) {
            case '.': return true; break;
            case '(': return true; break;
            case ')': return true; break;
            case '*': return true; break;
            case '|': return true; break;
        }
        return false;
    }
    
    function isRightParanthesis($caracter)
    {
        return ($caracter == ')');
    }
    
    function isLeftParanthesis($caracter)
    {
        return ($caracter == '(');
    }
    
   function opeval()
    {
            if(count($this->operadores)>0)
            {
                    // Extrae el ultimo operador del stack de operadores
                    $operador = array_pop($this->operadores);
    
                    // Comprueba que operador es
                    switch($operador)
                    {
                    case '*':
                            return $this->kleene();
                            break;
                    case '|':
                            return $this->union();
                            break;
                    case '.':
                            return $this->concat();
                            break;
                    }
    
                    return false;
            }
    
            return false;
    }
    
    function presedence($operador_izq, $operador_der)
    {
		if($operador_izq == $operador_der)
			return true;

		if($operador_izq == '*')
			return false;

		if($operador_der == '*')
			return true;

		if($operador_izq == '.')
			return false;

		if($operador_der == '.')
			return true;

		if($operador_izq == '|')
			return false;
		
		return true;
    }
    
    function generarAFND()
    {
        $this->regex = $this->concatExpand($this->regex);

        for($i=0; $i<strlen($this->regex); ++$i)
        {
          // obtiene el caracter
          $c = $this->regex[$i];
      
          if($this->isInput($c))
           $this->push($c);
          elseif(empty($this->operadores))
            $this->operadores[] = $c;
          elseif($this->isLeftParanthesis($c))
            $this->operadores[] = $c;
          elseif($this->isRightParanthesis($c))
          {
            // evalúa todo en el parentesis
            while(!$this->isLeftParanthesis($this->operadores[count($this->operadores)-1]))
              if(!$this->opeval())
                return false;
            // remueve el paretensis izquierdo despues de la evaluación
            array_pop($this->operadores);
          }
          else
          {
            while(!empty($this->operadores) && $this->presedence($c, $this->operadores[count($this->operadores)-1])) /**** AAAAAAAAAA ***/
              if(!$this->opeval())
                return false;
            $this->operadores[] = $c;
          }
        }
        
        
        // evalúa el resto de los operadores
          while(!empty($this->operadores))
            if(!$this->opeval())
              return FALSE;
        
          // extrae el resultado del stack
          if(empty($this->operandos) || !($AFND = array_pop($this->operandos)))
            return false;
        
          // El último estado del AFND es siempre un estado final
          $AFND->getEstado($AFND->getTotalEstados()-1)->setFinal(true);
          $AFND->setSimbolos($this->simbolos);

        $this->AFND = $AFND;

        return true;
    }
    
    function getAFND()
    {
        return $this->AFND;
    }
    
    function epsilonClosure($estados = array())
    {
        $res = array();
        $transiciones_vacias = array();
        
        if(isset($estados[0])) {
           $res[] = $estados[0]->getID();
           $transiciones_vacias[] = $estados[0]->getID();
        }
        
        while(!empty($estados))
        {
             // extrae el elemento superior del stack sin procesar
            $estado = array_pop($estados);
            
            // obtener todas las transiciones vacías para este estado
            $transiciones_vacias = $estado->getTransiciones('ε');
			
			// Para cada transición vacía del estado $estado
			$total_transiciones = count($transiciones_vacias);
			for($i = 0; $i < $total_transiciones; ++$i)
			{
				$u = $transiciones_vacias[$i];
				
				// si $u no está en las transiciones vacias
				if($u != 'Ф' && !in_array($u, $res) ) {
					// se agrega $u a las transiciones vacías
					$res[] = $u;
					// se guarda $u en el stack de estados sin procesar
					$estados[] = $this->AFND->getEstadoByID($u);
				}
			}
        }
                
        $resultados = array();
        
        foreach($res as $id) {
			// se guardan los estados encontrados
            $resultados[] = $this->AFND->getEstadoByID($id);
        }

        return $resultados;
    }
    
    function move($simbolo, $conjunto_estados = array())
    {
        $estados = array();
        foreach($conjunto_estados as $estado_trans) {
             $transiciones = $estado_trans->getTransiciones($simbolo);
            
             foreach($transiciones as $transicion) {
                if($transicion != 'Ф') 
                   $estados[] = $this->AFND->getEstadoByID($transicion);
             }
        }
        
        return $estados;
    }
    
    function AFNDaAFD()
    {
        $this->siguiente_ID = 1;
        
        $estados_desmarcados = array();
        $conjunto_estado_inicial_AFD = array();
        $conjunto_estado_inicial_AFND = array();
       
        // Extrae el estado inicial del AFND
        $tmp = $this->AFND->getEstados();
        if(empty($tmp))
			return;
        $conjunto_estado_inicial_AFND[] = $tmp[0];
       
        // Calcula las transiciones vacías para el estado inicial del AFND
        // y crea el conjunto de estados que formará el estado inicial del AFD
        $conjunto_estado_inicial_AFD = $this->epsilonClosure($conjunto_estado_inicial_AFND);

        // Crea nuevo estado inicial del AFD con el conjunto de estados
        $q0 = new Estado($this->siguiente_ID++, true, false);
        $q0->agregarConjunto($conjunto_estado_inicial_AFD);
       
        // Agrega el estado al AFD
        $this->AFD->setEstado($q0);
        foreach($this->simbolos as $simbolo) {
            if($simbolo != 'ε')
                $this->AFD->setSimbolo($simbolo);
        }

        // Agrega el estado inciial a un conjunto de estados no procesados del AFD
        $estados_desmarcados[] = $q0;
                
        while(!empty($estados_desmarcados))
        {
            // procesar un estado no procesado
            $estado_activo = array_pop($estados_desmarcados);
            $simbolos_AFD = $this->AFD->getSimbolos();
            
            foreach($simbolos_AFD as $simbolo) {
				// guarda el conjunto de estados resultante de aplicacar la función move en el estado activo
                $move_res = $this->move($simbolo, $estado_activo->getEstadosAFND());
                
                // guarda el conjunto de estados resultante de las transiciones vacías sobre el resultado anterior
                $epsilonclosure_res = $this->epsilonClosure($move_res);
                
                $encontrado = false;
                $s = null;
                $se = $this->AFD->getEstados();

				// busca si el conjunto de estados encontrado ya existía previamente en el AFD
                for($i = 0; $i < $this->AFD->getTotalEstados(); $i++) {
					$s = $se[$i];
                    
                    if($s->getEstadosAFND() === $epsilonclosure_res)
                    {
                        $encontrado = true;
						break;
                    }
                }
                
                // si el conjunto ya existía, no se procesa; en caso contrario:
				if(!$encontrado)
				{
					// se crea un nuevo estado en el AFD con el conjunto encontrado
					$U = new Estado($this->siguiente_ID++);
					$U->agregarConjunto($epsilonclosure_res);
					
					// Se agrega el estado a los estados desmarcados, para procesarlo
					$estados_desmarcados[] = $U;
					
					// Se agrega el estado al AFD
					$this->AFD->setEstado($U);
					
					// Agregar transicion de estado en proceso a nuevo estado con el simbolo actual
					$estado_activo->setTransicion($simbolo, $U);
				} else {
					$estado_activo->setTransicion($simbolo, $s);
				}
            }
        }
        
       $this->reducirAFD();
    }
    
    function reducirAFD()
    {
        $estados = $this->AFD->getEstados();
        $simbolos = $this->AFD->getSimbolos();
        $estados_basura = array();
        
        foreach($estados as $estado)
            if($estado->esEstadoBasura())
                $estados_basura[] = $estado;
        
        if(empty($estados_basura))
            return;
        
        foreach($estados_basura as $estado_basura) {
            // elimina transiciones hacia este estado basura
            foreach($estados as $estado)
                $estado->eliminaTransicion($estado_basura);
            
            // elimina el estado basura del AFD
            $this->AFD->eliminaEstado($estado_basura);
        }
    }
    
    function getAFD()
    {
        return $this->AFD;
    }
}
