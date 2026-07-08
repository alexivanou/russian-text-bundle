<?php

namespace AlexIvanou\RussianTextBundle\Tests;

use AlexIvanou\RussianTextBundle\Service\NameParser;
use PHPUnit\Framework\TestCase;

class NameParserTest extends TestCase
{
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new NameParser();
    }

    public function testParseFullFio()
    {
        $result = $this->parser->parse('Иванов Иван Иванович');
        $this->assertEquals('Иванов', $result->surname);
        $this->assertEquals('Иван', $result->firstName);
        $this->assertEquals('Иванович', $result->patronymic);
    }

    public function testParseWithoutPatronymic()
    {
        $result = $this->parser->parse('Петров Пётр');
        $this->assertEquals('Петров', $result->surname);
        $this->assertEquals('Пётр', $result->firstName);
        $this->assertNull($result->patronymic);
    }

    public function testParseSingleName()
    {
        $result = $this->parser->parse('Владимир');
        $this->assertEquals('Владимир', $result->surname);
        $this->assertNull($result->firstName);
        $this->assertNull($result->patronymic);
    }

    public function testParseFemale()
    {
        $result = $this->parser->parse('Смирнова Анна Сергеевна');
        $this->assertEquals('Анна', $result->firstName);
        $this->assertEquals('Сергеевна', $result->patronymic);
        $this->assertEquals('Смирнова', $result->surname);
        $this->assertEquals('w', $result->gender);
    }

    public function testParseMale()
    {
        $result = $this->parser->parse('Кузнецов Дмитрий Алексеевич');
        $this->assertEquals('Дмитрий', $result->firstName);
        $this->assertEquals('Алексеевич', $result->patronymic);
        $this->assertEquals('Кузнецов', $result->surname);
        $this->assertEquals('m', $result->gender);
    }

    public function testInititalsAfter()
    {
        $result = $this->parser->initials('Иванов Иван Петрович');
        $this->assertEquals('Иванов И. П.', $result);
    }

    public function testInititalsBefore()
    {
        $result = $this->parser->initials('Иванов Иван Петрович', 'before');
        $this->assertEquals('И. П. Иванов', $result);
    }

    public function testInititalsNoPatronymic()
    {
        $result = $this->parser->initials('Петров Пётр');
        $this->assertEquals('Петров П.', $result);
    }

    public function testInititalsSingleName()
    {
        $result = $this->parser->initials('Владимир');
        $this->assertEquals('Владимир', $result);
    }

    public function testInititalsEmpty()
    {
        $this->assertNull($this->parser->initials(''));
    }

    public function testInititalsNull()
    {
        $this->assertNull($this->parser->initials(null));
    }

    public function testParseEmpty()
    {
        $result = $this->parser->parse('');
        $this->assertNull($result->surname);
        $this->assertNull($result->firstName);
        $this->assertNull($result->patronymic);
    }

    public function testParseFirstNamePatronymic()
    {
        $result = $this->parser->parse('Николай Николаевич');
        $this->assertNull($result->surname);
        $this->assertEquals('Николай', $result->firstName);
        $this->assertEquals('Николаевич', $result->patronymic);
    }
}
