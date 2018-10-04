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

    public function devolverCantidad(){
        return $this->cantPreguntas;
    }
}