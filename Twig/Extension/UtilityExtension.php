<?php

namespace AlexIvanou\RussianTextBundle\Twig\Extension;

use AlexIvanou\RussianTextBundle\Service\TransliteratorInterface;
use AlexIvanou\RussianTextBundle\Service\TextHelperInterface;
use AlexIvanou\RussianTextBundle\Service\RussianDateFormatterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class UtilityExtension extends AbstractExtension
{
    private $transliterator;
    private $textHelper;
    private $dateFormatter;

    public function __construct(
        TransliteratorInterface $transliterator = null,
        TextHelperInterface $textHelper = null,
        RussianDateFormatterInterface $dateFormatter = null
    ) {
        $this->transliterator = $transliterator;
        $this->textHelper = $textHelper;
        $this->dateFormatter = $dateFormatter;
    }

    public function getFilters()
    {
        $filters = array();

        if ($this->transliterator !== null) {
            $filters[] = new TwigFilter('translit', array($this, 'translitFilter'));
            $filters[] = new TwigFilter('slug', array($this, 'slugFilter'));
        }

        if ($this->textHelper !== null) {
            $filters[] = new TwigFilter('truncate', array($this, 'truncateFilter'));
            $filters[] = new TwigFilter('ordinal_suffix', array($this, 'ordinalSuffixFilter'));
            $filters[] = new TwigFilter('currency_symbol', array($this, 'currencySymbolFilter'));
        }

        if ($this->dateFormatter !== null) {
            $filters[] = new TwigFilter('russian_date', array($this, 'russianDateFilter'));
            $filters[] = new TwigFilter('russian_date_genitive', array($this, 'russianDateGenitiveFilter'));
        }

        return $filters;
    }

    public function getFunctions()
    {
        $functions = array();

        if ($this->dateFormatter !== null) {
            $functions[] = new TwigFunction('russian_month', array($this, 'russianMonthFunction'));
            $functions[] = new TwigFunction('russian_day_of_week', array($this, 'russianDayOfWeekFunction'));
        }

        return $functions;
    }

    public function translitFilter($text, $iso = false)
    {
        return $this->transliterator->translit($text, $iso);
    }

    public function slugFilter($text, $separator = '-')
    {
        return $this->transliterator->slug($text, $separator);
    }

    public function truncateFilter($text, $length = 100, $suffix = '...')
    {
        return $this->textHelper->truncate($text, $length, $suffix);
    }

    public function ordinalSuffixFilter($number, $gender = 'm')
    {
        return $this->textHelper->ordinalSuffix($number, $gender);
    }

    public function currencySymbolFilter($currency)
    {
        return $this->textHelper->currencySymbol($currency);
    }

    public function russianDateFilter($date, $format = 'j F Y')
    {
        return $this->dateFormatter->format($date, $format);
    }

    public function russianDateGenitiveFilter($date, $format = 'j F Y')
    {
        return $this->dateFormatter->formatGenitive($date, $format);
    }

    public function russianMonthFunction($month, $case = 'nominative')
    {
        return $this->dateFormatter->monthName($month, $case);
    }

    public function russianDayOfWeekFunction($date, $case = 'nominative')
    {
        return $this->dateFormatter->dayOfWeek($date, $case);
    }
}
