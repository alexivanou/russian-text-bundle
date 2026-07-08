<?php

namespace AlexIvanou\RussianTextBundle\Service;

class RussianDateFormatter implements RussianDateFormatterInterface
{
    private static $monthsNominative = array(
        1 => 'январь', 2 => 'февраль', 3 => 'март',
        4 => 'апрель', 5 => 'май', 6 => 'июнь',
        7 => 'июль', 8 => 'август', 9 => 'сентябрь',
        10 => 'октябрь', 11 => 'ноябрь', 12 => 'декабрь',
    );

    private static $monthsGenitive = array(
        1 => 'января', 2 => 'февраля', 3 => 'марта',
        4 => 'апреля', 5 => 'мая', 6 => 'июня',
        7 => 'июля', 8 => 'августа', 9 => 'сентября',
        10 => 'октября', 11 => 'ноября', 12 => 'декабря',
    );

    private static $dayOfWeek = array(
        1 => 'понедельник', 2 => 'вторник', 3 => 'среда',
        4 => 'четверг', 5 => 'пятница', 6 => 'суббота',
        7 => 'воскресенье',
    );

    private static $dayOfWeekGenitive = array(
        1 => 'понедельника', 2 => 'вторника', 3 => 'среды',
        4 => 'четверга', 5 => 'пятницы', 6 => 'субботы',
        7 => 'воскресенья',
    );

    const MARKER_MONTH = "\x00\x01";
    const MARKER_MONTH_SHORT = "\x00\x02";
    const MARKER_DOW = "\x00\x03";
    const MARKER_DOW_SHORT = "\x00\x04";

    public function format($date, $format = 'j F Y')
    {
        return $this->doFormat($date, $format, 'nominative');
    }

    public function formatGenitive($date, $format = 'j F Y')
    {
        return $this->doFormat($date, $format, 'genitive');
    }

    public function monthName($month, $case = 'nominative')
    {
        $month = (int) $month;
        if ($month < 1 || $month > 12) {
            return null;
        }
        if ($case === 'genitive') {
            return self::$monthsGenitive[$month];
        }
        return self::$monthsNominative[$month];
    }

    public function dayOfWeek($date, $case = 'nominative')
    {
        $date = $this->normalizeDate($date);
        $dow = (int) $date->format('N');
        if ($case === 'genitive') {
            return self::$dayOfWeekGenitive[$dow];
        }
        return self::$dayOfWeek[$dow];
    }

    private function doFormat($date, $format, $case)
    {
        $date = $this->normalizeDate($date);

        $month = (int) $date->format('n');
        $dow = (int) $date->format('N');

        $replaceMap = array(
            'F' => self::MARKER_MONTH,
            'M' => self::MARKER_MONTH_SHORT,
            'l' => self::MARKER_DOW,
            'D' => self::MARKER_DOW_SHORT,
        );

        $safeFormat = str_replace(array_keys($replaceMap), array_values($replaceMap), $format);
        $result = $date->format($safeFormat);

        $monthStr = $case === 'genitive' ? self::$monthsGenitive[$month] : self::$monthsNominative[$month];
        $result = str_replace(self::MARKER_MONTH, $monthStr, $result);

        $monthShort = $this->getMonthShort($month);
        $result = str_replace(self::MARKER_MONTH_SHORT, $monthShort, $result);

        $dowStr = $case === 'genitive' ? self::$dayOfWeekGenitive[$dow] : self::$dayOfWeek[$dow];
        $result = str_replace(self::MARKER_DOW, $dowStr, $result);

        $dowShort = $this->getDayOfWeekShort($dow);
        $result = str_replace(self::MARKER_DOW_SHORT, $dowShort, $result);

        return $result;
    }

    private function normalizeDate($date)
    {
        if ($date instanceof \DateTime) {
            return $date;
        }
        if (is_numeric($date)) {
            return new \DateTime('@' . $date);
        }
        return new \DateTime($date);
    }

    private function getMonthShort($month)
    {
        $names = array(
            1 => 'янв', 2 => 'фев', 3 => 'мар', 4 => 'апр',
            5 => 'май', 6 => 'июн', 7 => 'июл', 8 => 'авг',
            9 => 'сен', 10 => 'окт', 11 => 'ноя', 12 => 'дек',
        );
        return $names[$month];
    }

    private function getDayOfWeekShort($dow)
    {
        $names = array(
            1 => 'пн', 2 => 'вт', 3 => 'ср', 4 => 'чт',
            5 => 'пт', 6 => 'сб', 7 => 'вс',
        );
        return $names[$dow];
    }
}
