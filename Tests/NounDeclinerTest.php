<?php

namespace AlexIvanou\RussianTextBundle\Tests;

use AlexIvanou\RussianTextBundle\Service\NounDecliner;
use PHPUnit\Framework\TestCase;

class NounDeclinerTest extends TestCase
{
    private $nounDecliner;

    protected function setUp(): void
    {
        $this->nounDecliner = new NounDecliner();
    }

    public function testDeclineFirstDeclension()
    {
        $this->assertEquals('машины', $this->nounDecliner->decline('машина', 'родительный'));
        $this->assertEquals('машиной', $this->nounDecliner->decline('машина', 'творительный'));
    }

    public function testDeclineSecondDeclension()
    {
        $this->assertEquals('дома', $this->nounDecliner->decline('дом', 'родительный'));
        $this->assertEquals('дому', $this->nounDecliner->decline('дом', 'дательный'));
    }

    public function testDeclineThirdDeclension()
    {
        $this->assertEquals('ночи', $this->nounDecliner->decline('ночь', 'родительный'));
        $this->assertEquals('ночью', $this->nounDecliner->decline('ночь', 'творительный'));
    }

    public function testGetCases()
    {
        $cases = $this->nounDecliner->getCases('дом');
        $this->assertTrue(is_array($cases));
        $this->assertArrayHasKey('nominative', $cases);
        $this->assertArrayHasKey('genitive', $cases);
        $this->assertEquals('дом', $cases['nominative']);
        $this->assertEquals('дома', $cases['genitive']);
    }

    public function testPluralize()
    {
        $this->assertEquals('домов', $this->nounDecliner->pluralize('дом', 5));
        $this->assertEquals('машины', $this->nounDecliner->pluralize('машина', 2));
    }

    public function testIsMutable()
    {
        $this->assertTrue($this->nounDecliner->isMutable('дом'));
        $this->assertFalse($this->nounDecliner->isMutable('евро'));
    }
}
