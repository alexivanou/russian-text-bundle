<?php

namespace AlexIvanou\RussianTextBundle\Twig\Extension;

use AlexIvanou\RussianTextBundle\Service\TimeSpellerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeExtension extends AbstractExtension
{
    private $timeSpeller;

    public function __construct(TimeSpellerInterface $timeSpeller)
    {
        $this->timeSpeller = $timeSpeller;
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('time_ago', array($this, 'timeAgoFilter')),
            new TwigFilter('distance_of_time', array($this, 'distanceOfTimeFilter')),
        );
    }

    public function timeAgoFilter($dateTime, $includeSeconds = false)
    {
        $dateTime = $this->normalizeDateTime($dateTime);
        if ($dateTime === null) {
            return 'неизвестно';
        }
        return $this->timeSpeller->timeAgo($dateTime, $includeSeconds);
    }

    public function distanceOfTimeFilter($fromTime, $toTime = null)
    {
        $fromTime = $this->normalizeDateTime($fromTime);
        if ($fromTime === null) {
            return 'неизвестно';
        }
        if ($toTime === null) {
            return $this->timeSpeller->timeAgo($fromTime);
        }
        $toTime = $this->normalizeDateTime($toTime);
        if ($toTime === null) {
            return 'неизвестно';
        }
        return $this->timeSpeller->spellDifference($fromTime);
    }

    private function normalizeDateTime($dateTime)
    {
        if ($dateTime instanceof \DateTimeInterface) {
            return $dateTime;
        }
        if (is_numeric($dateTime)) {
            $dt = new \DateTime();
            $dt->setTimestamp((int) $dateTime);
            return $dt;
        }
        if (is_string($dateTime)) {
            $ts = strtotime($dateTime);
            if ($ts !== false) {
                $dt = new \DateTime();
                $dt->setTimestamp($ts);
                return $dt;
            }
        }
        return null;
    }
}
