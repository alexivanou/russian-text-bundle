<?php

namespace AlexIvanou\RussianTextBundle\Tests;

use AlexIvanou\RussianTextBundle\Service\NumberSpeller;
use PHPUnit\Framework\TestCase;

class NumberSpellerTest extends TestCase
{
    private $numberSpeller;

    protected function setUp(): void
    {
        $this->numberSpeller = new NumberSpeller();
    }

    public function testCardinalSimple()
    {
        $this->assertEquals('один', $this->numberSpeller->cardinal(1));
        $this->assertEquals('два', $this->numberSpeller->cardinal(2));
        $this->assertEquals('сто двадцать три', $this->numberSpeller->cardinal(123));
    }

    public function testCardinalLarge()
    {
        $this->assertEquals('четыре тысячи триста пятьдесят один', $this->numberSpeller->cardinal(4351));
        $this->assertEquals('десять тысяч', $this->numberSpeller->cardinal(10000));
        $this->assertEquals('миллион', $this->numberSpeller->cardinal(1000000));
    }

    public function testOrdinalSimple()
    {
        $this->assertEquals('первый', $this->numberSpeller->ordinal(1));
        $this->assertEquals('второй', $this->numberSpeller->ordinal(2));
        $this->assertEquals('двадцать первый', $this->numberSpeller->ordinal(21));
    }

    public function testOrdinalLarge()
    {
        $this->assertEquals('девятьсот шестьдесят первый', $this->numberSpeller->ordinal(961));
    }
}
