<?php

namespace AlexIvanou\RussianTextBundle\Service;

use morphos\Russian\Plurality;

class Pluralizer implements PluralizerInterface
{
    public function pluralize($word, $count = 2, $animateness = false)
    {
        return Plurality::pluralize($word, $count, $animateness);
    }

    public function getNumeralForm($count)
    {
        return Plurality::getNumeralForm($count);
    }
}
