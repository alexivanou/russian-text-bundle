<?php

namespace AlexIvanou\RussianTextBundle\Tests;

use AlexIvanou\RussianTextBundle\Service\NameInflector;
use PHPUnit\Framework\TestCase;

class NameInflectorTest extends TestCase
{
    private $nameInflector;

    protected function setUp(): void
    {
        $this->nameInflector = new NameInflector();
    }

    public function testInflectTwoParts()
    {
        $result = $this->nameInflector->inflect('Иванов Иван', 'творительный');
        $this->assertEquals('Ивановым Иваном', $result);
    }

    public function testInflectThreeParts()
    {
        $result = $this->nameInflector->inflect('Иванов Иван Иванович', 'дательный');
        $this->assertEquals('Иванову Ивану Ивановичу', $result);
    }

    public function testDetectGender()
    {
        $this->assertEquals('m', $this->nameInflector->detectGender('Иванов Иван'));
        $this->assertEquals('w', $this->nameInflector->detectGender('Иванова Мария'));
    }

    public function testGetCases()
    {
        $cases = $this->nameInflector->getCases('Иванов Иван');
        $this->assertTrue(is_array($cases));
        $this->assertArrayHasKey('nominative', $cases);
        $this->assertArrayHasKey('genitive', $cases);
        $this->assertArrayHasKey('dative', $cases);
        $this->assertArrayHasKey('accusative', $cases);
        $this->assertArrayHasKey('ablative', $cases);
        $this->assertArrayHasKey('prepositional', $cases);
        $this->assertEquals('Иванов Иван', $cases['nominative']);
        $this->assertEquals('Иванова Ивана', $cases['genitive']);
    }
}
