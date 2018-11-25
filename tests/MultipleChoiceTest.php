<?php

namespace MultiChoice;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class MultipleChoiceTest extends TestCase{

    public function testCrearMultipleChoice(){
        $MultChoice = new MultipleChoice();
        $this->assertTrue(isset($MultChoice));
    }

    public function testMezclar(){
        $mult = new MultipleChoice();
        $preguntas = Yaml::parseFile('Preguntas/preguntas.yml');
        for($i = count($preguntas['preguntas']);$i>12;$i--){
            array_pop($preguntas['preguntas']);
        }
        $preguntasMezcladas1 = $mult->mezclar($mult->devolverPreguntas(0),$mult->devolverCantidad());
        $this->assertNotEquals($preguntas, $preguntasMezcladas1);
        $this->assertEquals(12, count($preguntasMezcladas1) );

        $mult = new MultipleChoice();
        $preguntasMezcladas2 = $mult->mezclar($mult->devolverPreguntas(0),$mult->devolverCantidad());
        $this->assertNotEquals($preguntas, $preguntasMezcladas2);
        $this->assertEquals(12, count($preguntasMezcladas2) );

        $mult = new MultipleChoice();
        $preguntasMezcladas3 = $mult->mezclar($mult->devolverPreguntas(0),$mult->devolverCantidad());
        $this->assertNotEquals($preguntas, $preguntasMezcladas3);
        $this->assertEquals(12, count($preguntasMezcladas3) );

        $this->assertNotEquals($preguntasMezcladas2,$preguntasMezcladas1);
        $this->assertNotEquals($preguntasMezcladas2,$preguntasMezcladas3);

    }

    public function testCantidadPreguntas(){
        $mult = new MultipleChoice(15);
        $this->assertEquals(15, $mult->devolverCantidad());
        $mult = new MultipleChoice(1);
        $this->assertEquals(1, $mult->devolverCantidad());
        $mult = new MultipleChoice(22);
        $this->assertEquals(22, $mult->devolverCantidad());
        $mult = new MultipleChoice(88);
        $this->assertEquals(26, $mult->devolverCantidad());
        $mult = new MultipleChoice(-88);
        $this->assertEquals(26, $mult->devolverCantidad());
    }

    public function testPreguntas(){
        $preguntas = Yaml::parseFile('Preguntas/preguntas.yml');
        $preguntas = $preguntas['preguntas'];
        $mult = new MultipleChoice(12,2,FALSE);
        for($i=count($preguntas);$i>12;$i--){
            array_pop($preguntas);
        }
        $this->assertEquals($preguntas,$mult->devolverPreguntas(0));
    }

    public function testEnunciado(){
        $preguntas = Yaml::parseFile('Preguntas/preguntas.yml')['preguntas'];
        $mult = new MultipleChoice(12,2,FALSE);
        for($i = 0; $i < 12;$i++){
            $this->assertEquals($preguntas[$i]['descripcion'], $mult->devolverEnunciado($mult->devolverPreguntas(0)[$i]));
        }
    }

    public function testRespuestas(){
        $preguntas = Yaml::parseFile('Preguntas/preguntas.yml')['preguntas'];
        $mult = new MultipleChoice(12, 2,FALSE);
        $respuestas = $preguntas[0]['respuestas_incorrectas'];
        $cant = count($preguntas[0]['respuestas_correctas']);
        for($i = 0; $i<$cant;$i++){
            array_push($respuestas,$preguntas[0]['respuestas_correctas'][$i]);
        }
        $this->assertEquals($mult->devolverRespuestas($mult->devolverPreguntas(0)[0]), $respuestas);


        $respuestas = $preguntas[1]['respuestas_incorrectas'];
        $cant = count($preguntas[1]['respuestas_correctas']);
        for($i = 0; $i<$cant;$i++){
            array_push($respuestas,$preguntas[1]['respuestas_correctas'][$i]);
        }
        $this->assertEquals($mult->devolverRespuestas($mult->devolverPreguntas(0)[1]), $respuestas);
        
        
        $respuestas = $preguntas[2]['respuestas_incorrectas'];
        $cant = count($preguntas[2]['respuestas_correctas']);
        for($i = 0; $i<$cant;$i++){
            array_push($respuestas,$preguntas[2]['respuestas_correctas'][$i]);
        }
        $this->assertEquals($mult->devolverRespuestas($mult->devolverPreguntas(0)[2]), $respuestas);
        
    }

    public function testGenerarPregunta(){
        $mult = new MultipleChoice();
        
        $pregunta = $mult->devolverPreguntas(0)[0];
        $pregunta = $mult->inicializarRespuestas($pregunta);
        $preguntaRealizada = $mult->generarPregunta($pregunta,1,1);
        $pregunta['respuestas'] = array_merge($pregunta['respuestas_correctas'],$pregunta['respuestas_incorrectas']);
        unset($pregunta['respuestas_correctas'],$pregunta['respuestas_incorrectas']);
        
        $this->assertEquals($pregunta['descripcion'],$preguntaRealizada['descripcion']);
        foreach($pregunta['respuestas'] as $rtas){
            $this->assertContains($rtas,$pregunta['respuestas']);
        }

        $pregunta = $mult->devolverPreguntas(0)[1];
        $pregunta = $mult->inicializarRespuestas($pregunta);
        $preguntaRealizada = $mult->generarPregunta($pregunta,1,1);
        $pregunta['respuestas'] = array_merge($pregunta['respuestas_correctas'],$pregunta['respuestas_incorrectas']);
        unset($pregunta['respuestas_correctas'],$pregunta['respuestas_incorrectas']);
        
        $this->assertEquals($pregunta['descripcion'],$preguntaRealizada['descripcion']);
        foreach($pregunta['respuestas'] as $rtas){
            $this->assertContains($rtas,$pregunta['respuestas']);
        }
    }

    public function testSwapOpcionesFinales(){
        $mult = new MultipleChoice();
        $opcionesFinales = ['Todas las anteriores','Ninguna de las anteriores'];
        $opcionesFinalesaux = ['Ninguna de las anteriores','Todas las anteriores'];
        $opcionesFinalesaux = $mult->swapOpcionesFinales($opcionesFinalesaux);
        $this->assertEquals($opcionesFinales,$opcionesFinalesaux);
        $opcionesFinales = ['Todas las anteriores','Ninguno de los anteriores.'];
        $opcionesFinalesaux = ['Ninguno de los anteriores.','Todas las anteriores'];
        $opcionesFinalesaux = $mult->swapOpcionesFinales($opcionesFinalesaux);
        $this->assertEquals($opcionesFinales,$opcionesFinalesaux);
    }

    public function testTextoNingunaDeLasAnteriores(){
        $mult = new MultipleChoice(12,2,FALSE);
        $preguntas = Yaml::parseFile('Preguntas/preguntas.yml')['preguntas'];
        $this->assertEquals("Ninguna de las anteriores", $mult->textoNingunaDeLasAnteriores($preguntas[0]));
        $this->assertEquals("Ninguna de las anteriores", $mult->textoNingunaDeLasAnteriores($preguntas[1]));
        $this->assertEquals("Ninguno de los anteriores.", $mult->textoNingunaDeLasAnteriores($preguntas[12]));   
    }

    public function testNingunaDeLasAnteriores(){
        $mult = new MultipleChoice();
        $preguntas = Yaml::parseFile('Preguntas/preguntas.yml')['preguntas'];
        $this->assertEquals("respuestas_incorrectas", $mult->ningunaDeLasAnteriores($preguntas[0]['respuestas_correctas']));
        $this->assertEquals("respuestas_correctas", $mult->ningunaDeLasAnteriores($preguntas[1]['respuestas_correctas']));
        $this->assertEquals("respuestas_incorrectas", $mult->ningunaDeLasAnteriores($preguntas[2]['respuestas_correctas']));
    }
}