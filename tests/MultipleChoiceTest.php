<?php

namespace MultiChoice;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class MultipleChoiceTest extends TestCase{

    public function testPrimero(){
        $MultChoice = new MultipleChoice();
        $this->assertTrue(isset($MultChoice));
    }

    public function testMezclar(){
        $mult = new MultipleChoice();
        $preguntas = Yaml::parseFile('Preguntas/preguntas.yml');
        for($i = count($preguntas['preguntas']);$i>12;$i--){
            array_pop($preguntas['preguntas']);
        }
        $preguntasMezcladas1 = $mult->mezclar($mult->devolverPreguntas(),$mult->devolverCantidad());
        $this->assertNotEquals($preguntas, $preguntasMezcladas1);
        $this->assertEquals(12, count($preguntasMezcladas1) );

        $mult = new MultipleChoice();
        $preguntasMezcladas2 = $mult->mezclar($mult->devolverPreguntas(),$mult->devolverCantidad());
        $this->assertNotEquals($preguntas, $preguntasMezcladas2);
        $this->assertEquals(12, count($preguntasMezcladas2) );

        $mult = new MultipleChoice();
        $preguntasMezcladas3 = $mult->mezclar($mult->devolverPreguntas(),$mult->devolverCantidad());
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
        $this->assertEquals(12, $mult->devolverCantidad());
        $mult = new MultipleChoice(-88);
        $this->assertEquals(12, $mult->devolverCantidad());
    }

    public function testPreguntas(){
        $preguntas = Yaml::parseFile('Preguntas/preguntas.yml');
        $preguntas = $preguntas['preguntas'];
        $mult = new MultipleChoice();
        $this->assertEquals($preguntas,$mult->devolverPreguntas());        
    }
}