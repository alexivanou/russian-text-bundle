<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface TimeSpellerInterface
{
    public function spellDifference(\DateTimeInterface $dateTime, $options = 0, $limit = 0);
    public function timeAgo(\DateTimeInterface $dateTime, $includeSeconds = false);
}
