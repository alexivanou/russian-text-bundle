<?php

namespace AlexIvanou\RussianTextBundle\Tests;

use AlexIvanou\RussianTextBundle\Service\GeographicalNameInflector;
use PHPUnit\Framework\TestCase;

class GeographicalNameInflectorTest extends TestCase
{
    private $inflector;

    protected function setUp(): void
    {
        $this->inflector = new GeographicalNameInflector();
    }

    public function testStandardCity()
    {
        $this->assertEquals('Москвы', $this->inflector->inflect('Москва', 'genitive'));
        $this->assertEquals('Москве', $this->inflector->inflect('Москва', 'dative'));
    }

    public function testMasculineCity()
    {
        $this->assertEquals('Парижа', $this->inflector->inflect('Париж', 'genitive'));
        $this->assertEquals('Парижем', $this->inflector->inflect('Париж', 'ablative'));
    }

    public function testOvCity()
    {
        $this->assertEquals('Саратова', $this->inflector->inflect('Саратов', 'genitive'));
        $this->assertEquals('Саратову', $this->inflector->inflect('Саратов', 'dative'));
        $this->assertEquals('Саратовом', $this->inflector->inflect('Саратов', 'ablative'));
    }

    public function testSkCity()
    {
        $this->assertEquals('Томска', $this->inflector->inflect('Томск', 'genitive'));
        $this->assertEquals('Томску', $this->inflector->inflect('Томск', 'dative'));
    }

    public function testImmutableCity()
    {
        $this->assertEquals('Сочи', $this->inflector->inflect('Сочи', 'genitive'));
        $this->assertEquals('Баку', $this->inflector->inflect('Баку', 'dative'));
    }

    public function testGetCases()
    {
        $cases = $this->inflector->getCases('Москва');
        $this->assertTrue(is_array($cases));
        $this->assertArrayHasKey('nominative', $cases);
        $this->assertArrayHasKey('genitive', $cases);
        $this->assertEquals('Москва', $cases['nominative']);
        $this->assertEquals('Москвы', $cases['genitive']);
    }

    public function testIsMutable()
    {
        $this->assertTrue($this->inflector->isMutable('Москва'));
        $this->assertFalse($this->inflector->isMutable('Сочи'));
    }
}
