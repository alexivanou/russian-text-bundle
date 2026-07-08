<?php

namespace AlexIvanou\RussianTextBundle\Tests;

use AlexIvanou\RussianTextBundle\Service\TextHelper;
use PHPUnit\Framework\TestCase;

class TextHelperTest extends TestCase
{
    private $helper;

    protected function setUp(): void
    {
        $this->helper = new TextHelper();
    }

    public function testOrdinalSuffixMale()
    {
        $this->assertEquals('1-й', $this->helper->ordinalSuffix(1));
        $this->assertEquals('2-й', $this->helper->ordinalSuffix(2));
        $this->assertEquals('3-й', $this->helper->ordinalSuffix(3));
        $this->assertEquals('10-й', $this->helper->ordinalSuffix(10));
        $this->assertEquals('21-й', $this->helper->ordinalSuffix(21));
    }

    public function testOrdinalSuffixFemale()
    {
        $this->assertEquals('1-я', $this->helper->ordinalSuffix(1, 'f'));
        $this->assertEquals('2-я', $this->helper->ordinalSuffix(2, 'f'));
        $this->assertEquals('10-я', $this->helper->ordinalSuffix(10, 'f'));
    }

    public function testOrdinalSuffixNeuter()
    {
        $this->assertEquals('1-е', $this->helper->ordinalSuffix(1, 'n'));
        $this->assertEquals('2-е', $this->helper->ordinalSuffix(2, 'n'));
        $this->assertEquals('10-е', $this->helper->ordinalSuffix(10, 'n'));
    }

    public function testCurrencySymbol()
    {
        $this->assertEquals('₽', $this->helper->currencySymbol('RUB'));
        $this->assertEquals('$', $this->helper->currencySymbol('USD'));
        $this->assertEquals('€', $this->helper->currencySymbol('EUR'));
        $this->assertEquals('Br', $this->helper->currencySymbol('BYN'));
    }

    public function testCurrencySymbolUnknown()
    {
        $this->assertEquals('XYZ', $this->helper->currencySymbol('XYZ'));
    }

    public function testTruncateShort()
    {
        $result = $this->helper->truncate('Короткий текст', 100);
        $this->assertEquals('Короткий текст', $result);
    }

    public function testTruncateLong()
    {
        $result = $this->helper->truncate('Очень длинный текст для проверки обрезки', 20);
        $this->assertStringEndsWith('...', $result);
    }

    public function testTruncateShortEnough()
    {
        $result = $this->helper->truncate('Текст', 5, '...');
        $this->assertEquals('Текст', $result);
    }

    public function testTruncateExact()
    {
        $result = $this->helper->truncate('Длинный текст', 8, '...');
        $this->assertEquals('Длинный...', $result);
    }

    public function testPluralForm()
    {
        $this->assertEquals('рубль', TextHelper::pluralForm(1, 'рубль', 'рубля', 'рублей'));
        $this->assertEquals('рубля', TextHelper::pluralForm(2, 'рубль', 'рубля', 'рублей'));
        $this->assertEquals('рублей', TextHelper::pluralForm(5, 'рубль', 'рубля', 'рублей'));
        $this->assertEquals('рублей', TextHelper::pluralForm(11, 'рубль', 'рубля', 'рублей'));
        $this->assertEquals('рубль', TextHelper::pluralForm(21, 'рубль', 'рубля', 'рублей'));
    }
}
