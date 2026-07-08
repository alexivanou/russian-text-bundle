<?php

namespace AlexIvanou\RussianTextBundle\Tests;

use AlexIvanou\RussianTextBundle\Service\TimeSpeller;
use PHPUnit\Framework\TestCase;

class TimeSpellerTest extends TestCase
{
    private $timeSpeller;

    protected function setUp(): void
    {
        $this->timeSpeller = new TimeSpeller();
    }

    public function testNow()
    {
        $this->assertEquals('только что', $this->timeSpeller->timeAgo(new \DateTime('now')));
    }

    public function testMinutesAgo()
    {
        $this->assertEquals('5 минут назад', $this->timeSpeller->timeAgo(new \DateTime('-5 minutes')));
    }

    public function testHourAgo()
    {
        $this->assertEquals('1 час назад', $this->timeSpeller->timeAgo(new \DateTime('-1 hour')));
    }

    public function testHoursAgo()
    {
        $this->assertEquals('23 часа назад', $this->timeSpeller->timeAgo(new \DateTime('-23 hours')));
    }

    public function testDayAgo()
    {
        $this->assertEquals('1 день назад', $this->timeSpeller->timeAgo(new \DateTime('-1 day')));
    }

    public function testMonthAgo()
    {
        $this->assertEquals('1 месяц назад', $this->timeSpeller->timeAgo(new \DateTime('-45 days')));
    }

    public function testFuture()
    {
        $this->assertEquals('через 5 минут', $this->timeSpeller->timeAgo(new \DateTime('+5 minutes')));
    }

    public function testFutureDay()
    {
        $this->assertEquals('через 1 день', $this->timeSpeller->timeAgo(new \DateTime('+1 day')));
    }

    public function testFutureHour()
    {
        $this->assertEquals('через 2 часа', $this->timeSpeller->timeAgo(new \DateTime('+2 hours')));
    }

    public function testNoDirection()
    {
        $this->assertEquals('5 минут', $this->timeSpeller->spellDifference(new \DateTime('-5 minutes'), 0));
    }
}
