<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface RussianDateFormatterInterface
{
    public function format($date, $format = 'j F Y');
    public function formatGenitive($date, $format = 'j F Y');
    public function monthName($month, $case = 'nominative');
    public function dayOfWeek($date, $case = 'nominative');
}
