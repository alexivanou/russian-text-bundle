<?php

namespace AlexIvanou\RussianTextBundle\Service;

use morphos\Russian\FirstNamesDeclension;
use morphos\Russian\LastNamesDeclension;
use morphos\Russian\MiddleNamesDeclension;

class NameInflector implements NameInflectorInterface
{
    public function inflect($fullName, $case, $gender = null)
    {
        return \morphos\Russian\name($fullName, $case, $gender);
    }

    public function getCases($fullName, $gender = null)
    {
        return \morphos\Russian\name($fullName, null, $gender);
    }

    public function detectGender($fullName)
    {
        return \morphos\Russian\detectGender($fullName);
    }

    public function firstNameCase($name, $case, $gender = null)
    {
        $first = new FirstNamesDeclension();
        return $first->getCase($name, $case, $gender);
    }

    public function lastNameCase($name, $case, $gender = null)
    {
        $last = new LastNamesDeclension();
        return $last->getCase($name, $case, $gender);
    }

    public function middleNameCase($name, $case, $gender = null)
    {
        $middle = new MiddleNamesDeclension();
        return $middle->getCase($name, $case, $gender);
    }

    public function firstNameCases($name, $gender = null)
    {
        $first = new FirstNamesDeclension();
        return $first->getCases($name, $gender);
    }

    public function lastNameCases($name, $gender = null)
    {
        $last = new LastNamesDeclension();
        return $last->getCases($name, $gender);
    }

    public function middleNameCases($name, $gender = null)
    {
        $middle = new MiddleNamesDeclension();
        return $middle->getCases($name, $gender);
    }
}
