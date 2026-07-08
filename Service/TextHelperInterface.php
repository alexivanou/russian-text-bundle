<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface TextHelperInterface
{
    public function ordinalSuffix($number, $gender = 'm');
    public function currencySymbol($currency);
    public function truncate($text, $length = 100, $suffix = '...');
}
