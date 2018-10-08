<?php

namespace MultiChoice;

use Symfony\Component\Yaml\Yaml;

class MultipleChoice{
    protected $cantPreguntas;
    protected $preguntas;
    
    public function __construct($cantPreguntas = 12){

        $this->preguntas = Yaml::parseFile('Preguntas/preguntas.yml');

        if($cantPreguntas < count($this->preguntas['preguntas']) && $cantPreguntas > 0){
            $this->cantPreguntas = $cantPreguntas;
        }else{
            $this->cantPreguntas = 12;            
        }



    }

    public function organizar(){
        $this->preguntas = $this->mezclar($this->preguntas['preguntas'],$this->cantPreguntas);
        for($i=0;$i<$this->cantPreguntas;$i++){
            $this->preguntas[$i] = $this->inicializarRespuestas($this->preguntas[$i]);
            print_r($this->devolverRespuestas($this->preguntas[$i]));
        }
    }


    public function mezclar($array, $cant){
        shuffle($array);
        for($i = count($array);$i>$cant;$i--){
            array_pop($array);
        }

        return $array;
    }

    public function devolverPreguntas(){
        return $this->preguntas['preguntas'];
    }

    public function devolverEnunciado($pregunta){
        return $pregunta['descripcion'];
    }

    public function devolverCantidad(){
        return $this->cantPreguntas;
    }

    public function devolverRespuestas($pregunta){
        $aux = $pregunta['respuestas_incorrectas'];
        $cant = count($pregunta['respuestas_correctas']);
        for($i = 0; $i<$cant;$i++){
            array_push($aux,$pregunta['respuestas_correctas'][$i]);
        }
        return $aux;
    }

    public function inicializarRespuestas($pregunta){
        $cant = count($pregunta['respuestas_incorrectas']);

        if($cant == 0){
            $pregunta['respuestas_incorrectas'] = '1Todas las anteriores';
        }

        for($i=0;$i<$cant;$i++){
            $pregunta['respuestas_incorrectas'][$i] = "0" . $pregunta['respuestas_incorrectas'][$i];
        }

        $cant = count($pregunta['respuestas_correctas']);

        if($cant == 0){
            $pregunta['respuestas_incorrectas'] = '1Ninguna de las anteriores';
        }

        for($i=0;$i<$cant;$i++){
            $pregunta['respuestas_correctas'][$i] = "1" . $pregunta['respuestas_correctas'][$i];
        }
        
        return $pregunta;
    }
}