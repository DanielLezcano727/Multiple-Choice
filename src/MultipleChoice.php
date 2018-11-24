<?php

namespace MultiChoice;

use Symfony\Component\Yaml\Yaml;

class MultipleChoice {
    protected $cantPreguntas;
    protected $preguntas;
    protected $preguntasElegidas;
    protected $mezclarPreguntas;

    public function __construct($cantPreguntas = 12, $mezclar = TRUE) {

        $this->preguntas = Yaml::parseFile('Preguntas/preguntas.yml');

        if ($cantPreguntas <= count($this->preguntas['preguntas']) && $cantPreguntas > 0) {
            $this->cantPreguntas = $cantPreguntas;
        } else {
            $this->cantPreguntas = 12;
        }
        $this->mezclarPreguntas = $mezclar;
        $this->organizar();
    }

    /**
     * Mezcla las preguntas y elige las que van a ser utilizadas para el examen
     *
     */
    public function organizar() {

        $this->preguntas = $this->mezclar($this->preguntas['preguntas'], $this->cantPreguntas);


        $this->preguntasElegidas = $this->preguntas;

        for ($i = 0; $i < $this->cantPreguntas; $i++) {
            $this->preguntas[$i] = $this->inicializarRespuestas($this->preguntas[$i]);
            $this->preguntas[$i] = $this->generarPregunta($this->preguntas[$i]);
        }
    }

    public function multipleChoice(){
        $mostrar = "";
        foreach($this->preguntas as $preg){
            $mostrar .= $this->devolverEnunciado($preg) . "\n";
            foreach($preg['respuestas'] as $rtas){
                $mostrar .= "   " . $rtas . "\n";
            }
            $mostrar .= "\n\n\n";
        }
        return $mostrar;
    }

    /**
     * Mezcla las preguntas y elimina las que sobren. Devuelve las preguntas restantes.
     *
     * @param array $array
     *   Array de preguntas que se va a mezclar
     * @param int $cant
     *   Cantidad de preguntas que se van a obtener
     * 
     * @return array
     */
    public function mezclar($array, $cant) {
        if($this->mezclarPreguntas){
            shuffle($array);
        }
        for ($i = count($array); $i > $cant; $i--) {
            array_pop($array);
        }

        return $array;
    }

    /**
     * Crea un array con la respuesta organizada en enunciado y en respuestas
     *
     * @param array $pregunta
     *    La pregunta que se quiere organizar
     *
     * @return array
     */
    public function generarPregunta($pregunta) {
        $nuevaPregunta['descripcion'] = $pregunta['descripcion'];
        
        $nuevaPregunta['respuestas'] = $this->devolverRespuestas($pregunta);

        shuffle($nuevaPregunta['respuestas']);

        $cant = count($nuevaPregunta['respuestas']);

        $opcionesFinales = [];

        for($i=0;$i<$cant;$i++){
            $todas = $nuevaPregunta['respuestas'][$i] == "Todas las anteriores";
            $ninguna = $nuevaPregunta['respuestas'][$i] == "Ninguna de las anteriores";
            if($todas || $ninguna){
                array_push($opcionesFinales,$nuevaPregunta['respuestas'][$i]);
                unset($nuevaPregunta['respuestas'][$i]);
            }
        }
        if(count($opcionesFinales) == 2 && $opcionesFinales[0] == "Ninguna de las anteriores"){
            $tmp = $opcionesFinales[0];
            $opcionesFinales[0] = $opcionesFinales[1];
            $opcionesFinales[1] = $tmp;
        }
        $nuevaPregunta['respuestas'] = array_merge($nuevaPregunta['respuestas'],$opcionesFinales);
        return $nuevaPregunta;
    }

    /**
     * Devuelve un array que posee solo las preguntas con sus respuestas
     *
     * @return array
     */
    public function devolverPreguntas() {
        return $this->preguntasElegidas;
    }

    /**
     * Devuelve el enunciado de la consigna.
     *
     * @param array $pregunta
     *
     * @return string
     */
    public function devolverEnunciado($pregunta) {
        return $pregunta['descripcion'];
    }

    /**
     * Devuelve la cantidad de preguntas que decidio utilizar el usuario
     *
     * @return int
     */
    public function devolverCantidad() {
        return $this->cantPreguntas;
    }

    /**
     * Devuelve un array que posee las respuestas correctas e incorrectas
     *
     * @param array $pregunta
     *
     * @return array
     */
    public function devolverRespuestas($pregunta) {
        return array_merge($pregunta['respuestas_incorrectas'],$pregunta['respuestas_correctas']);
    }

    /**
     * Recuerda las respuestas correctas e incorrectas y devuelve la pregunta con las respuestas modificadas
     *
     * @return array
     */
    public function inicializarRespuestas($pregunta) {

        $todasLasAnteriores = FALSE;

        if(array_key_exists('ocultar_opcion_todas_las_anteriores',$pregunta)){
            $todasLasAnteriores = $pregunta['ocultar_opcion_todas_las_anteriores'];
        }

        if(!$todasLasAnteriores){
            array_push($pregunta['respuestas_incorrectas'], 'Todas las anteriores');
        }

        $ningunaLasAnteriores = FALSE;

        if(array_key_exists('ocultas_opcion_ninguna_de_las_anteriores',$pregunta)){
            $ningunaLasAnteriores = $pregunta['ocultas_opcion_ninguna_de_las_anteriores'];
        }

        if(!$ningunaLasAnteriores){
            array_push($pregunta['respuestas_incorrectas'], 'Ninguna de las anteriores');
        }
        return $pregunta;
    }
}