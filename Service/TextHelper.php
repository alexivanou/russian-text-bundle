<?php

namespace AlexIvanou\RussianTextBundle\Service;

use morphos\S;

class TextHelper implements TextHelperInterface
{
    private static $currencySymbols = array(
        'RUB' => '₽',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'BYN' => 'Br',
        'UAH' => '₴',
        'KZT' => '₸',
        'JPY' => '¥',
        'CNY' => '¥',
        'CHF' => 'Fr',
    );

    public function ordinalSuffix($number, $gender = 'm')
    {
        if ($gender === 'f') {
            return $number . '-я';
        }
        if ($gender === 'n') {
            return $number . '-е';
        }
        return $number . '-й';
    }

    public function currencySymbol($currency)
    {
        $currency = strtoupper($currency);
        if (isset(self::$currencySymbols[$currency])) {
            return self::$currencySymbols[$currency];
        }
        return $currency;
    }

    public function truncate($text, $length = 100, $suffix = '...')
    {
        if (S::length($text) <= $length) {
            return $text;
        }

        $truncated = S::slice($text, 0, $length);
        $lastSpace = S::length($truncated);

        for ($i = $length - 1; $i >= 0; $i--) {
            $char = S::slice($truncated, $i, $i + 1);
            if ($char === ' ' || $char === "\t" || $char === "\n") {
                $lastSpace = $i;
                break;
            }
        }

        if ($lastSpace === $length) {
            return $truncated . $suffix;
        }

        return S::slice($text, 0, $lastSpace) . $suffix;
    }

    public static function pluralForm($count, $form1, $form2, $form5)
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
