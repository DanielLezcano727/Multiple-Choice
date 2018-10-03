<?php

namespace Multiple_Choice;

use PHPUnit\Framework\TestCase;

class MultipleChoiceTest extends TestCase{
    public function testPrimero(){
        $MultChoice = new MultipleChoice();
        $this->assertTrue(isset($MultChoice));
    }
}