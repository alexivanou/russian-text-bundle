<?php

namespace AlexIvanou\RussianTextBundle\Tests;

use AlexIvanou\RussianTextBundle\Service\MoneySpeller;
use PHPUnit\Framework\TestCase;

class MoneySpellerTest extends TestCase
{
    private $moneySpeller;

    protected function setUp(): void
    {
        $this->moneySpeller = new MoneySpeller();
    }

    public function testSpellRubNormal()
    {
        $result = $this->moneySpeller->spell(1, MoneySpeller::RUBLE);
        $this->assertEquals('один рубль', $result);
    }

    public function testSpellRubPlural()
    {
        $result = $this->moneySpeller->spell(2, MoneySpeller::RUBLE);
        $this->assertEquals('два рубля', $result);
    }

    public function testSpellRubLarge()
    {
        $result = $this->moneySpeller->spell(100, MoneySpeller::RUBLE);
        $this->assertEquals('сто рублей', $result);
    }

    public function testSpellRubWithKopecks()
    {
        $result = $this->moneySpeller->spell(123.45, MoneySpeller::RUBLE);
        $this->assertEquals('сто двадцать три рубля сорок пять копеек', $result);
    }

    public function testSpellRubZero()
    {
        $result = $this->moneySpeller->spell(0.50, MoneySpeller::RUBLE);
        $this->assertEquals('ноль рублей пятьдесят копеек', $result);
    }

    public function testSpellShort()
    {
        $result = $this->moneySpeller->spell(123.45, MoneySpeller::RUBLE, 'short');
        $this->assertEquals('123 рубля 45 копеек', $result);
    }

    public function testSpellUsd()
    {
        $result = $this->moneySpeller->spell(10.50, MoneySpeller::DOLLAR);
        $this->assertEquals('десять долларов пятьдесят центов', $result);
    }

    public function testSpellEur()
    {
        $result = $this->moneySpeller->spell(100, MoneySpeller::EURO);
        $this->assertEquals('сто евро', $result);
    }

    public function testSpellByn()
    {
        $result = $this->moneySpeller->spell(5, MoneySpeller::BELARUSIAN_RUBLE);
        $this->assertEquals('пять рублей', $result);
    }

    public function testSpellBynWithKopecks()
    {
        $result = $this->moneySpeller->spell(2.50, MoneySpeller::BELARUSIAN_RUBLE);
        $this->assertEquals('два рубля пятьдесят копеек', $result);
    }

    public function testSymbolRub()
    {
        $this->assertEquals('₽', $this->moneySpeller->symbol(MoneySpeller::RUBLE));
    }

    public function testSymbolUsd()
    {
        $this->assertEquals('$', $this->moneySpeller->symbol(MoneySpeller::DOLLAR));
    }

    public function testSymbolByn()
    {
        $this->assertEquals('Br', $this->moneySpeller->symbol(MoneySpeller::BELARUSIAN_RUBLE));
    }

    public function testSymbolUnknown()
    {
        $this->assertEquals('XYZ', $this->moneySpeller->symbol('XYZ'));
    }

    public function testDefaultSymbol()
    {
        $this->assertEquals('₽', $this->moneySpeller->symbol());
    }
}
