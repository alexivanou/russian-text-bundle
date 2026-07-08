<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface MoneySpellerInterface
{
    public function spell($value, $currency = 'RUB', $format = 'normal');
    public function symbol($currency = 'RUB');
}
