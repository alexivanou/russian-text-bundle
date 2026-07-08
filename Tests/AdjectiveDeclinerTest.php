<?php

namespace AlexIvanou\RussianTextBundle\Tests;

use AlexIvanou\RussianTextBundle\Service\AdjectiveDecliner;
use PHPUnit\Framework\TestCase;

class AdjectiveDeclinerTest extends TestCase
{
    private $decliner;

    protected function setUp(): void
    {
        $this->decliner = new AdjectiveDecliner();
    }

    public function testHardMasculine()
    {
        $this->assertEquals('нового', $this->decliner->decline('новый', 'genitive'));
    }

    public function testHardFeminine()
    {
        $this->assertEquals('новая', $this->decliner->decline('новый', 'nominative', 'f'));
        $this->assertEquals('новой', $this->decliner->decline('новый', 'genitive', 'f'));
        $this->assertEquals('новую', $this->decliner->decline('новый', 'accusative', 'f'));
    }

    public function testHardNeuter()
    {
        $this->assertEquals('новое', $this->decliner->decline('новый', 'nominative', 'n'));
    }

    public function testHardPlural()
    {
        $this->assertEquals('новые', $this->decliner->plural('новый'));
        $this->assertEquals('новых', $this->decliner->plural('новый', 'genitive'));
    }

    public function testSoftMasculine()
    {
        $this->assertEquals('синего', $this->decliner->decline('синий', 'genitive'));
        $this->assertEquals('синим', $this->decliner->decline('синий', 'ablative'));
    }

    public function testSoftFeminine()
    {
        $this->assertEquals('синяя', $this->decliner->decline('синий', 'nominative', 'f'));
        $this->assertEquals('синюю', $this->decliner->decline('синий', 'accusative', 'f'));
    }

    public function testGuttural()
    {
        $this->assertEquals('русского', $this->decliner->decline('русский', 'genitive'));
        $this->assertEquals('русским', $this->decliner->decline('русский', 'ablative'));
        $this->assertEquals('великого', $this->decliner->decline('великий', 'genitive'));
    }

    public function testGetCases()
    {
        $cases = $this->decliner->getCases('новый');
        $this->assertTrue(is_array($cases));
        $this->assertArrayHasKey('nominative', $cases);
        $this->assertArrayHasKey('genitive', $cases);
        $this->assertEquals('новый', $cases['nominative']);
        $this->assertEquals('нового', $cases['genitive']);
    }

    public function testPluralCases()
    {
        $cases = $this->decliner->pluralCases('новый');
        $this->assertTrue(is_array($cases));
        $this->assertEquals('новые', $cases['nominative']);
        $this->assertEquals('новых', $cases['genitive']);
    }
}
