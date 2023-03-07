<?php

use TestCase;
// unit test
class TestClass extends TestCase {

    public function TestAdd() {
        $a = 10;
        $B = 20;
        $this->assertEquals(30, $a + $B);
    }
}