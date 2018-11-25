<?php

namespace MultiChoice;

use Symfony\Component\Yaml\Yaml;

class MultipleChoice {
    protected $cantPreguntas;
    protected $preguntas;
    protected $temaCorrecto;
    protected $mezclarPreguntas;
    public $tema;

    public function __construct($cantPreguntas = 12, $temas = 2, $mezclar = TRUE) {

        $preguntasCompletas = Yaml::parseFile('Preguntas/preguntas.yml');
        $cantPreguntasArchivo = count($preguntasCompletas['preguntas']);
        if ($cantPreguntas <= $cantPreguntasArchivo && $cantPreguntas > 0) {
            $this->cantPreguntas = $cantPreguntas;
        } else {
            $this->cantPreguntas = $cantPreguntasArchivo;
        }
        $this->mezclarPreguntas = $mezclar;
        for($i=0;$i<$temas;$i++){
            $this->tema[$i] = $this->organizar($preguntasCompletas, $i);
        }
    }

    /**
     * Mezcla las preguntas y elige las que van a ser utilizadas para el examen
     *
     */
    public function organizar($preguntas, $tema) {

        $preguntas = $this->mezclar($preguntas['preguntas'], $this->cantPreguntas);


        $this->temaCorrecto[$tema] = $preguntas;

        for ($i = 0; $i < $this->cantPreguntas; $i++) {
            $preguntas[$i] = $this->inicializarRespuestas($preguntas[$i]);
            $preguntas[$i] = $this->generarPregunta($preguntas[$i]);
        }
        return $preguntas;
    }

    public function multipleChoice($tema){
        $mostrar = "";
        foreach($this->tema[$tema] as $preg){
            $mostrar .= $this->devolverEnunciado($preg) . "\n";
            $cantResp = 0;
            foreach($preg['respuestas'] as $rtas){
                $mostrar .= "   " . chr($cantResp + ord('A')). ")" . $rtas . "\n";
                $cantResp++;
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
        $aux = $this->generarVariasCorrectas($nuevaPregunta,$pregunta);
        $nuevaPregunta = $aux[0];
        $pregunta = $aux[1];
        $nuevaPregunta['respuestas'] = array_merge($nuevaPregunta['respuestas'],$opcionesFinales);
        return $nuevaPregunta;
    }

    /**
     * Devuelve un array que posee solo las preguntas con sus respuestas
     *
     * @return array
     */
    public function devolverPreguntas($tema) {
        return $this->temaCorrecto[$tema];
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

        $cantIncorrectas = count($pregunta['respuestas_incorrectas']);

        if(!$todasLasAnteriores){
            $tipo = "";
            if($cantIncorrectas == 0){
                $tipo = 'respuestas_correctas';
                $aux = $pregunta[$tipo];
                unset($pregunta[$tipo]);
                $pregunta['respuestas_incorrectas'] = $aux;
                $pregunta[$tipo] = [];
            }else{
                $tipo = 'respuestas_incorrectas'; 
            }
            array_push($pregunta[$tipo], 'Todas las anteriores');
        }

        $ningunaLasAnteriores = FALSE;

        if(array_key_exists('ocultas_opcion_ninguna_de_las_anteriores',$pregunta)){
            $ningunaLasAnteriores = $pregunta['ocultas_opcion_ninguna_de_las_anteriores'];
        }
        $cantCorrectas = count($pregunta['respuestas_correctas']);
        if(!$ningunaLasAnteriores){
            $tipo = "";
            if($cantIncorrectas == 0){
                $tipo = 'respuestas_correctas';
            }else{
                $tipo = 'respuestas_incorrectas'; 
            }
            array_push($pregunta['respuestas_incorrectas'], 'Ninguna de las anteriores');
        }
        return $pregunta;
    }

    public function generarVariasCorrectas($preguntaMezclada, $pregunta){
        $cantCorrectas = count($pregunta['respuestas_correctas']);
        
        if($cantCorrectas<2){
            return [$preguntaMezclada,$pregunta];
        }
        $preguntaMezclada['respuestas'] = array_values($preguntaMezclada['respuestas']);
        $correctas = [];
        $cantRespuestas = count($preguntaMezclada['respuestas']);
        for($i=0;$i<$cantCorrectas;$i++){
            $correcta = $pregunta['respuestas_correctas'][$i];
            for($j=0;$j<$cantRespuestas;$j++){
                if($preguntaMezclada['respuestas'][$j] == $correcta){
                    array_push($correctas, chr($j + ord('A')));
                }
            }
        }
        sort($correctas);
        $correctasOpc = $correctas[0];
        for($i=1;$i<$cantCorrectas-1;$i++){
            $correctasOpc .= ", " . $correctas[$i];
        }
        $correctasOpc .= " y " . $correctas[$cantCorrectas-1];
        array_push($preguntaMezclada['respuestas'],$correctasOpc);
        array_push($pregunta['respuestas_correctas'],$correctasOpc);
        return [$preguntaMezclada,$pregunta];
    }
}