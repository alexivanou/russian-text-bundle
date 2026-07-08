<?php

namespace AlexIvanou\RussianTextBundle\Service;

class TimeSpeller implements TimeSpellerInterface
{
    const DIRECTION = 1;
    const SEPARATE = 2;

    public function spellDifference(\DateTimeInterface $dateTime, $options = 0, $limit = 0)
    {
        $timestamp = $dateTime->getTimestamp();
        $now = time();
        $diff = $timestamp - $now;
        $future = $diff > 0;
        $diff = abs($diff);

        if ($diff < 60) {
            $result = 'только что';
            if ($options & self::DIRECTION) {
                return $result;
            }
        } elseif ($diff < 3600) {
            $minutes = (int) floor($diff / 60);
            $result = $minutes . ' ' . $this->pluralForm($minutes, 'минута', 'минуты', 'минут');
        } elseif ($diff < 86400) {
            $hours = (int) floor($diff / 3600);
            $result = $hours . ' ' . $this->pluralForm($hours, 'час', 'часа', 'часов');
        } elseif ($diff < 2592000) {
            $days = (int) floor($diff / 86400);
            $result = $days . ' ' . $this->pluralForm($days, 'день', 'дня', 'дней');
        } elseif ($diff < 31536000) {
            $months = (int) floor($diff / 2592000);
            $result = $months . ' ' . $this->pluralForm($months, 'месяц', 'месяца', 'месяцев');
        } else {
            $years = (int) floor($diff / 31536000);
            $result = $years . ' ' . $this->pluralForm($years, 'год', 'года', 'лет');
        }

        if ($options & self::DIRECTION) {
            if ($future) {
                $result = 'через ' . $result;
            } else {
                $result .= ' назад';
            }
        }

        return $result;
    }

    public function timeAgo(\DateTimeInterface $dateTime, $includeSeconds = false)
    {
        return $this->spellDifference($dateTime, self::DIRECTION);
    }

    private function pluralForm($count, $form1, $form2, $form5)
    {
        $n = abs($count) % 100;
        if ($n > 10 && $n < 20) {
            return $form5;
        }
        $n %= 10;
        if ($n == 1) {
            return $form1;
        }
        if ($n >= 2 && $n <= 4) {
            return $form2;
        }
        return $form5;
    }
}
