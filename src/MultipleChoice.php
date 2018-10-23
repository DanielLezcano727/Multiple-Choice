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

    /**
     * Mezcla las preguntas y elige las que van a ser utilizadas para el examen
     * 
     */
    public function organizar(){
        $this->preguntas = $this->mezclar($this->preguntas['preguntas'],$this->cantPreguntas);
        for($i=0;$i<$this->cantPreguntas;$i++){
            $this->preguntas[$i] = $this->inicializarRespuestas($this->preguntas[$i]);
            print_r($this->devolverRespuestas($this->preguntas[$i]));
        }
    }

    /**
     * Mezcla las preguntas y elimina las que sobren. Devuelve las preguntas restantes.
     * 
     * @param array $array
     * @param int $cant
     * 
     * @return array
     */
    public function mezclar($array, $cant){
        shuffle($array);
        for($i = count($array);$i>$cant;$i--){
            array_pop($array);
        }

        return $array;
    }

    /**
     * Devuelve un array que posee solo las preguntas con sus respuestas
     * 
     * @return array
     */
    public function devolverPreguntas(){
        return $this->preguntas['preguntas'];
    }

    /**
     * Devuelve el enunciado de la consigna.
     * 
     * @param array $pregunta
     * 
     * @return string
     */
    public function devolverEnunciado($pregunta){
        return $pregunta['descripcion'];
    }

    /**
     * Devuelve la cantidad de preguntas que decidio utilizar el usuario
     * 
     * @return int
     */
    public function devolverCantidad(){
        return $this->cantPreguntas;
    }

    /**
     * Devuelve un array que posee las respuestas correctas e incorrectas
     * 
     * @param array $pregunta
     * 
     * @return array
     */
    public function devolverRespuestas($pregunta){
        $aux = $pregunta['respuestas_incorrectas'];
        $cant = count($pregunta['respuestas_correctas']);
        for($i = 0; $i<$cant;$i++){
            array_push($aux,$pregunta['respuestas_correctas'][$i]);
        }
        return $aux;
    }

    /**
     * Recuerda las respuestas correctas e incorrectas y devuelve la pregunta con las respuestas modificadas
     * 
     * @return array
     */
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