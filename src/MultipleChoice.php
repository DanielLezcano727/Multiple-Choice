<?php

namespace MultiChoice;

use Symfony\Component\Yaml\Yaml;

class MultipleChoice {
    protected $cantPreguntas;
    protected $preguntas;
    protected $temaCorrecto;
    protected $mezclarPreguntas;
    protected $tema;

    public function __construct($cantPreguntas = 12, $temas = 2, $mezclar = TRUE) {

        $preguntasCompletas = Yaml::parseFile('Preguntas/preguntas.yml');
        $cantPreguntasArchivo = count($preguntasCompletas['preguntas']);
        if ($cantPreguntas <= $cantPreguntasArchivo && $cantPreguntas > 0) {
            $this->cantPreguntas = $cantPreguntas;
        }else {
            $this->cantPreguntas = $cantPreguntasArchivo;
        }
        $this->mezclarPreguntas = $mezclar;
        for ($i = 0;$i < $temas;$i++) {
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

        for ($i = 0;$i < $this->cantPreguntas;$i++) {
            $preguntas[$i] = $this->inicializarRespuestas($preguntas[$i]);
            $this->temaCorrectoAux[$tema][$i] = $preguntas[$i];
            $preguntas[$i] = $this->generarPregunta($preguntas[$i], $tema, $i);
        }
        return $preguntas;
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
        if ($this->mezclarPreguntas) {
            shuffle($array);
        }
        for ($i = count($array);$i > $cant;$i--) {
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
    public function generarPregunta($pregunta, $tema, $numero) {
        $nuevaPregunta['descripcion'] = $pregunta['descripcion'];
        
        $nuevaPregunta['respuestas'] = $this->devolverRespuestas($pregunta);

        shuffle($nuevaPregunta['respuestas']);

        $cant = count($nuevaPregunta['respuestas']);

        $opcionesFinales = [];

        for ($i = 0;$i < $cant;$i++) {
            $todas = $nuevaPregunta['respuestas'][$i] == "Todas las anteriores";
            $ninguna = $nuevaPregunta['respuestas'][$i] == "Ninguna de las anteriores";
            $ninguna = $ninguna || $nuevaPregunta['respuestas'][$i] == "Ninguno de los anteriores.";
            if ($todas || $ninguna) {
                array_push($opcionesFinales, $nuevaPregunta['respuestas'][$i]);
                unset($nuevaPregunta['respuestas'][$i]);
            }
        }
        $opcionesFinales = $this->swapOpcionesFinales($opcionesFinales);
        $aux = $this->generarVariasCorrectas($nuevaPregunta, $pregunta);
        $nuevaPregunta = $aux[0];
        $pregunta = $aux[1];
        $this->temaCorrectoAux[$tema][$numero] = $aux[1];
        $nuevaPregunta['respuestas'] = array_merge($nuevaPregunta['respuestas'], $opcionesFinales);
        return $nuevaPregunta;
    }

    public function swapOpcionesFinales($opcionesFinales){
        if (count($opcionesFinales) == 2 && ($opcionesFinales[0] == "Ninguna de las anteriores" || $opcionesFinales[0] == "Ninguno de los anteriores.")) {
            $tmp = $opcionesFinales[0];
            $opcionesFinales[0] = $opcionesFinales[1];
            $opcionesFinales[1] = $tmp;
        }
        return $opcionesFinales;
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
        return array_merge($pregunta['respuestas_incorrectas'], $pregunta['respuestas_correctas']);
    }

    /**
     * Recuerda las respuestas correctas e incorrectas y devuelve la pregunta con las respuestas modificadas
     *
     * @return array
     */
    public function inicializarRespuestas($pregunta) {

        $todasLasAnteriores = FALSE;

        if (array_key_exists('ocultar_opcion_todas_las_anteriores', $pregunta)) {
            $todasLasAnteriores = $pregunta['ocultar_opcion_todas_las_anteriores'];
        }

        $cantIncorrectas = count($pregunta['respuestas_incorrectas']);

        if (!$todasLasAnteriores) {
            $tipo = "";
            if ($cantIncorrectas == 0) {
                $tipo = 'respuestas_correctas';
                $aux = $pregunta[$tipo];
                unset($pregunta[$tipo]);
                $pregunta['respuestas_incorrectas'] = array_merge($pregunta['respuestas_incorrectas'], $aux);
                $pregunta[$tipo] = [];
            }else {
                $tipo = 'respuestas_incorrectas'; 
            }
            array_push($pregunta[$tipo], 'Todas las anteriores');
        }

        $ningunaLasAnteriores = FALSE;

        if (array_key_exists('ocultas_opcion_ninguna_de_las_anteriores', $pregunta)) {
            $ningunaLasAnteriores = $pregunta['ocultas_opcion_ninguna_de_las_anteriores'];
        }
        $cantCorrectas = count($pregunta['respuestas_correctas']);
        if (!$ningunaLasAnteriores) {
            $tipo = "";
            if ($cantCorrectas == 0) {
                $tipo = 'respuestas_correctas';
            }else {
                $tipo = 'respuestas_incorrectas'; 
            }

            if (array_key_exists('texto_ninguna_de_las_anteriores', $pregunta)) {
                $texto = $pregunta['texto_ninguna_de_las_anteriores'];
            }else {
                $texto = 'Ninguna de las anteriores';
            }

            array_push($pregunta[$tipo], $texto);
        }
        
        return $pregunta;
    }

    public function generarVariasCorrectas($preguntaMezclada, $pregunta) {
        $cantCorrectas = count($pregunta['respuestas_correctas']);
        
        if ($cantCorrectas < 2) {
            return [$preguntaMezclada, $pregunta];
        }
        $preguntaMezclada['respuestas'] = array_values($preguntaMezclada['respuestas']);
        $correctas = [];
        $nocorrectas = [];
        $cantRespuestas = count($preguntaMezclada['respuestas']);
        for ($i = 0;$i < count($preguntaMezclada['respuestas']);$i++) {
            array_push($nocorrectas, chr($i + ord('A')));
        }
        for ($i = 0;$i < $cantCorrectas;$i++) {
            $correcta = $pregunta['respuestas_correctas'][$i];
            for ($j = 0;$j < $cantRespuestas;$j++) {
                if ($preguntaMezclada['respuestas'][$j] == $correcta) {
                    array_push($correctas, chr($j + ord('A')));
                }
            }
        }

        $aux = $pregunta['respuestas_correctas'];
        unset($pregunta['respuestas_correctas']);
        $pregunta['respuestas_incorrectas'] = array_merge($pregunta['respuestas_incorrectas'], $aux);
        $pregunta['respuestas_correctas'] = [];

        unset($nocorrectas[array_search($correctas[0], $nocorrectas)]);
        for ($i = 0;$i < 2;$i++) {
            $aux = array_rand($nocorrectas, 2);
            $aux2 = $nocorrectas[$aux[0]] . " y " . $nocorrectas[$aux[1]];
            unset($nocorrectas[$aux[0]]);
            array_push($preguntaMezclada['respuestas'], $aux2);
            array_push($pregunta['respuestas_incorrectas'], $aux2);
        }
        sort($correctas);
        $correctasOpc = $correctas[0];
        for ($i = 1;$i < $cantCorrectas - 1;$i++) {
            $correctasOpc .= ", " . $correctas[$i];
        }
        $correctasOpc .= " y " . $correctas[$cantCorrectas - 1];

        
        array_push($preguntaMezclada['respuestas'], $correctasOpc);
        array_push($pregunta['respuestas_correctas'], $correctasOpc);
        return [$preguntaMezclada, $pregunta];
    }

    public function generarPrueba($tema, $resolucion) {
        $mostrar = $this->cabecera($tema);
        $preguntaNro = 1;
        foreach ($this->tema[$tema] as $preg) {
            $mostrar .= "<div class='question'>
            <div class='number'>" . $preguntaNro . ")__";
            $mostrar .= $this->respuesta($preg,$resolucion,$tema,$preguntaNro-1);
            $mostrar .= "___</div>";
            $mostrar .= "\n<div class='description'>" . $this->devolverEnunciado($preg) . "</div>
            <div class='options short'>";
            $mostrar .= $this->mostrarRespuestas($preg);
            $mostrar .= "
            </div>
          </div>";
            $preguntaNro++;
        }
        $mostrar .= "
        </div>
      </body>
    </html>";
        return $mostrar;
    }

    public function respuesta($preg,$resolucion,$tema,$preguntaNro){
        if ($resolucion) {
            $contador = 0;
            foreach ($preg['respuestas'] as $rta) {
                if ($rta == $this->temaCorrectoAux[$tema][$preguntaNro]['respuestas_correctas'][0]) {
                    break;
                }
                $contador++;
            }
            return chr($contador + ord('A'));
        }
        return "";             
    }

    public function mostrarRespuestas($preg){
        $cantResp = 0;
        $aux = "";
        foreach ($preg['respuestas'] as $rtas) {
            $aux .= "
            <div class='option'>" . chr($cantResp + ord('A')) . ")";
            $aux .= $rtas . "</div>";
            $cantResp++;
        }
        return $aux;
    }

    public function cabecera($tema) {
        $aux = '
        <!DOCTYPE html>
        <html>
          <head>
            <title>Exam</title>
            <meta charset="utf-8">
            <meta name="description" content="">
            <meta name=viewport content="width=device-width, initial-scale=1">
        
            <style>
              .question {
                  border: 1px solid gray;
                  padding: 0.3em;
              }
              .number {
                  float: left;
                  margin-right: 0.5em;
                  font-weight: bold;
              }
              .options {
                  display: flex;
                  flex-direction: column;
              }
              .short {
                  display: grid;
                  grid-template-columns: 1fr 1fr;
                  grid-gap: 1em;
              }
              .questions {
                  display: grid;
                  grid-template-columns: 1fr 1fr;
                  grid-gap: 1em 1em;
              }
              .header {
                  display: flex;
                  justify-content: space-between;
                  margin-bottom: 1em;
              }
              .description {
                  margin-bottom: 0.5em;
                  font-weight: bold;
              }
              body {
                font-size: 12px;
              }
            </style>
          </head>
          <body>
            <div class="header">
              <strong>Nombre y Apellido _____________________________________________ </strong>
              <strong>Evaluación número 1 ' . date("d/m/y", time()) . '</strong>
              <strong>TEMA ' . ($tema + 1) . '</strong>
              </div>
              <div class="questions">';
        return $aux;
    }
}