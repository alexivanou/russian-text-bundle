<?php

namespace AlexIvanou\RussianTextBundle\Service;

use morphos\Russian\CardinalNumeral;
use morphos\Russian\OrdinalNumeral;
use morphos\NumeralCreation;

class NumberSpeller implements NumberSpellerInterface
{
    public function cardinal($number, $gender = NumeralCreation::MALE)
    {
        return CardinalNumeral::generate($number, $gender);
    }

    public function cardinalCase($number, $case, $gender = NumeralCreation::MALE)
    {
        $cardinal = new CardinalNumeral();
        return $cardinal->getCase($number, $case, $gender);
    }

    public function cardinalCases($number, $gender = NumeralCreation::MALE)
    {
        $cardinal = new CardinalNumeral();
        return $cardinal->getCases($number, $gender);
    }

    public function ordinal($number, $gender = NumeralCreation::MALE)
    {
        return OrdinalNumeral::generate($number, $gender);
    }

    public function ordinalCase($number, $case, $gender = NumeralCreation::MALE)
    {
        $ordinal = new OrdinalNumeral();
        return $ordinal->getCase($number, $case, $gender);
    }

    public function ordinalCases($number, $gender = NumeralCreation::MALE)
    {
        $ordinal = new OrdinalNumeral();
        return $ordinal->getCases($number, $gender);
    }
}
