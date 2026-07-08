<?php

namespace AlexIvanou\RussianTextBundle\Tests;

use AlexIvanou\RussianTextBundle\Service\Pluralizer;
use PHPUnit\Framework\TestCase;

class PluralizerTest extends TestCase
{
    private $pluralizer;

    protected function setUp(): void
    {
        $this->pluralizer = new Pluralizer();
    }

    public function testPluralizeOne()
    {
        $this->assertEquals('дом', $this->pluralizer->pluralize('дом', 1));
    }

    public function testPluralizeTwoFour()
    {
        $this->assertEquals('дома', $this->pluralizer->pluralize('дом', 2));
        $this->assertEquals('дома', $this->pluralizer->pluralize('дом', 3));
        $this->assertEquals('дома', $this->pluralizer->pluralize('дом', 4));
    }

    public function testPluralizeFiveOther()
    {
        $this->assertEquals('домов', $this->pluralizer->pluralize('дом', 5));
        $this->assertEquals('домов', $this->pluralizer->pluralize('дом', 10));
        $this->assertEquals('домов', $this->pluralizer->pluralize('дом', 20));
    }

    public function testPluralizeElevenNineteen()
    {
        $this->assertEquals('домов', $this->pluralizer->pluralize('дом', 11));
        $this->assertEquals('домов', $this->pluralizer->pluralize('дом', 12));
        $this->assertEquals('домов', $this->pluralizer->pluralize('дом', 14));
    }

    public function testPluralizeTwentyOne()
    {
        $this->assertEquals('дом', $this->pluralizer->pluralize('дом', 21));
    }

    public function testGetNumeralForm()
    {
        $this->assertEquals(1, $this->pluralizer->getNumeralForm(1));
        $this->assertEquals(2, $this->pluralizer->getNumeralForm(2));
        $this->assertEquals(2, $this->pluralizer->getNumeralForm(3));
        $this->assertEquals(2, $this->pluralizer->getNumeralForm(4));
        $this->assertEquals(3, $this->pluralizer->getNumeralForm(5));
        $this->assertEquals(3, $this->pluralizer->getNumeralForm(10));
        $this->assertEquals(3, $this->pluralizer->getNumeralForm(11));
        $this->assertEquals(1, $this->pluralizer->getNumeralForm(21));
        $this->assertEquals(2, $this->pluralizer->getNumeralForm(22));
        $this->assertEquals(3, $this->pluralizer->getNumeralForm(100));
    }
}
