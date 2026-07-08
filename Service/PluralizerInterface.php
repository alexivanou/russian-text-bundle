<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface PluralizerInterface
{
    public function pluralize($word, $count);
    public function getNumeralForm($count);
}
