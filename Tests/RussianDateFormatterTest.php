<?php

namespace AlexIvanou\RussianTextBundle\Tests;

use AlexIvanou\RussianTextBundle\Service\RussianDateFormatter;
use PHPUnit\Framework\TestCase;

class RussianDateFormatterTest extends TestCase
{
    private $formatter;

    protected function setUp(): void
    {
        $this->formatter = new RussianDateFormatter();
    }

    public function testFormat()
    {
        $date = new \DateTime('2026-01-15');
        $result = $this->formatter->format($date, 'j F Y');
        $this->assertEquals('15 январь 2026', $result);
    }

    public function testFormatNominative()
    {
        $date = new \DateTime('2026-03-05');
        $result = $this->formatter->format($date, 'j F Y');
        $this->assertEquals('5 март 2026', $result);
    }

    public function testFormatWithTime()
    {
        $date = new \DateTime('2026-06-10 14:30');
        $result = $this->formatter->format($date, 'j F Y H:i');
        $this->assertEquals('10 июнь 2026 14:30', $result);
    }

    public function testFormatDayOfWeek()
    {
        $date = new \DateTime('2026-07-07');
        $result = $this->formatter->format($date, 'l');
        $this->assertEquals('вторник', $result);
    }

    public function testFormatShortMonth()
    {
        $date = new \DateTime('2026-12-25');
        $result = $this->formatter->format($date, 'j M Y');
        $this->assertEquals('25 дек 2026', $result);
    }

    public function testFormatGenitive()
    {
        $date = new \DateTime('2026-07-07');
        $result = $this->formatter->formatGenitive($date, 'j F Y');
        $this->assertEquals('7 июля 2026', $result);
    }

    public function testFormatGenitiveFromString()
    {
        $result = $this->formatter->formatGenitive('2026-10-05', 'j F Y');
        $this->assertEquals('5 октября 2026', $result);
    }

    public function testMonthName()
    {
        $this->assertEquals('январь', $this->formatter->monthName(1));
        $this->assertEquals('декабрь', $this->formatter->monthName(12));
    }

    public function testMonthNameGenitive()
    {
        $this->assertEquals('января', $this->formatter->monthName(1, 'genitive'));
        $this->assertEquals('декабря', $this->formatter->monthName(12, 'genitive'));
    }

    public function testMonthNameInvalid()
    {
        $this->assertNull($this->formatter->monthName(13));
        $this->assertNull($this->formatter->monthName(0));
    }

    public function testDayOfWeek()
    {
        $date = new \DateTime('2026-01-12');
        $this->assertEquals('понедельник', $this->formatter->dayOfWeek($date));
    }

    public function testDayOfWeekGenitive()
    {
        $date = new \DateTime('2026-01-12');
        $this->assertEquals('понедельника', $this->formatter->dayOfWeek($date, 'genitive'));
    }

    public function testFormatFromString()
    {
        $result = $this->formatter->format('2026-07-07', 'j F Y');
        $this->assertEquals('7 июль 2026', $result);
    }
}
