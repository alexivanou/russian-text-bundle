<?php

namespace AlexIvanou\RussianTextBundle\Service;

use morphos\NumeralCreation;

class MoneySpeller implements MoneySpellerInterface
{
    const RUBLE = 'RUB';
    const DOLLAR = 'USD';
    const EURO = 'EUR';
    const HRYVNIA = 'UAH';
    const TENGE = 'KZT';
    const BELARUSIAN_RUBLE = 'BYN';

    private static $currencies = array(
        'RUB' => array(
            array('рубль', 'рубля', 'рублей'),
            array('копейка', 'копейки', 'копеек'),
            NumeralCreation::MALE,
        ),
        'USD' => array(
            array('доллар', 'доллара', 'долларов'),
            array('цент', 'цента', 'центов'),
            NumeralCreation::MALE,
        ),
        'EUR' => array(
            array('евро', 'евро', 'евро'),
            array('цент', 'цента', 'центов'),
            NumeralCreation::MALE,
        ),
        'UAH' => array(
            array('гривна', 'гривны', 'гривен'),
            array('копейка', 'копейки', 'копеек'),
            NumeralCreation::FEMALE,
        ),
        'KZT' => array(
            array('тенге', 'тенге', 'тенге'),
            array('тиын', 'тиына', 'тиынов'),
            NumeralCreation::MALE,
        ),
        'BYN' => array(
            array('рубль', 'рубля', 'рублей'),
            array('копейка', 'копейки', 'копеек'),
            NumeralCreation::MALE,
        ),
    );

    private static $currencySymbols = array(
        'RUB' => '₽',
        'USD' => '$',
        'EUR' => '€',
        'UAH' => '₴',
        'KZT' => '₸',
        'BYN' => 'Br',
    );

    private $numberSpeller;

    public function __construct()
    {
        $this->numberSpeller = new NumberSpeller();
    }

    public function symbol($currency = self::RUBLE)
    {
        if (isset(self::$currencySymbols[$currency])) {
            return self::$currencySymbols[$currency];
        }
        return $currency;
    }

    public function spell($value, $currency = self::RUBLE, $format = 'normal')
    {
        if (!isset(self::$currencies[$currency])) {
            $currency = self::RUBLE;
        }

        $info = self::$currencies[$currency];
        list($majorForms, $minorForms, $gender) = $info;

        $parts = explode('.', sprintf('%.2f', (float) $value));
        $major = (int) $parts[0];
        $minor = (int) $parts[1];

        switch ($format) {
            case 'short':
                return $this->formatNumber($major) . ' ' . $this->pluralForm($major, $majorForms)
                     . ' ' . sprintf('%02d', $minor) . ' ' . $this->pluralForm($minor, $minorForms);

            case 'accounting':
                $result = $major == 0 ? 'ноль' : $this->numberSpeller->cardinal($major, $gender);
                $result .= ' ' . $this->pluralForm($major, $majorForms);
                $result .= ' ' . sprintf('%02d', $minor) . ' ' . $this->pluralForm($minor, $minorForms);
                return $result;

            case 'clarification':
                $words = $major == 0 ? 'ноль' : $this->numberSpeller->cardinal($major, $gender);
                $result = $this->formatNumber($major) . ' (' . $words . ') ';
                $result .= $this->pluralForm($major, $majorForms);
                $result .= ' ' . sprintf('%02d', $minor) . ' (' . $this->numberSpeller->cardinal($minor, NumeralCreation::FEMALE) . ') ';
                $result .= $this->pluralForm($minor, $minorForms);
                return $result;

            case 'normal':
            default:
                $result = $major == 0 ? 'ноль' : $this->numberSpeller->cardinal($major, $gender);
                $result .= ' ' . $this->pluralForm($major, $majorForms);
                if ($minor > 0) {
                    $result .= ' ' . $this->numberSpeller->cardinal($minor, NumeralCreation::FEMALE);
                    $result .= ' ' . $this->pluralForm($minor, $minorForms);
                }
                return $result;
        }
    }

    private function formatNumber($number)
    {
        return number_format($number, 0, '.', ' ');
    }

    private function pluralForm($count, array $forms)
    {
        $count = abs($count) % 100;
        if ($count > 10 && $count < 20) {
            return $forms[2];
        }
        $count %= 10;
        if ($count == 1) {
            return $forms[0];
        }
        if ($count >= 2 && $count <= 4) {
            return $forms[1];
        }
        return $forms[2];
    }
}
