<?php

namespace AlexIvanou\RussianTextBundle\Service;

use morphos\S;
use morphos\Russian\FirstNamesDeclension;
use morphos\Russian\MiddleNamesDeclension;

class NameParser implements NameParserInterface
{
    private $firstNames;
    private $middleNames;

    public function __construct()
    {
        $this->firstNames = new FirstNamesDeclension();
        $this->middleNames = new MiddleNamesDeclension();
    }

    public function parse($fullName)
    {
        $fullName = trim($fullName);
        if ($fullName === '') {
            return new NameComponents(null, null, null, null);
        }

        $parts = preg_split('/\s+/', $fullName);

        $count = count($parts);

        if ($count === 1) {
            return new NameComponents($parts[0], null, null, null);
        }

        if ($count === 2) {
            $lower = S::lower($parts[1]);
            $patrGender = $this->middleNames->detectGender($lower);
            if ($patrGender !== null) {
                $firstName = $parts[0];
                $patronymic = $parts[1];
                $gender = $patrGender === 'm' ? 'm' : 'w';
                return new NameComponents(null, $firstName, $patronymic, $gender);
            }
            $surname = $parts[0];
            $firstName = $parts[1];
            $gender = $this->detectGender($parts[0], $parts[1]);
            return new NameComponents($surname, $firstName, null, $gender);
        }

        $surname = $parts[0];
        $firstName = $parts[1];
        $patronymic = $parts[2];
        $gender = $this->detectGender($firstName, $patronymic, $surname);

        return new NameComponents($surname, $firstName, $patronymic, $gender);
    }

    public function initials($fullName, $style = 'after')
    {
        if ($fullName === null || $fullName === '') {
            return null;
        }

        $components = $this->parse($fullName);

        if ($components->firstName === null && $components->patronymic === null) {
            return $components->surname;
        }

        $initialFirst = $components->firstName ? S::upper(S::slice($components->firstName, 0, 1)) . '.' : '';
        $initialPatr = $components->patronymic ? S::upper(S::slice($components->patronymic, 0, 1)) . '.' : '';
        $surname = $components->surname ?: '';

        if ($style === 'before') {
            $initials = trim($initialFirst . ' ' . $initialPatr);
            return trim($initials . ' ' . $surname);
        }

        return trim($surname . ' ' . $initialFirst . ' ' . $initialPatr);
    }

    private function detectGender(...$words)
    {
        foreach ($words as $word) {
            if ($word === null) continue;
            $lower = S::lower($word);
            $patrGender = $this->middleNames->detectGender($lower);
            if ($patrGender !== null) {
                return $patrGender === 'm' ? 'm' : 'w';
            }
            $firstGender = $this->firstNames->detectGender($lower);
            if ($firstGender !== null) {
                return $firstGender === 'm' ? 'm' : 'w';
            }
        }
        return null;
    }
}

class NameComponents
{
    public $surname;
    public $firstName;
    public $patronymic;
    public $gender;

    public function __construct($surname, $firstName, $patronymic, $gender)
    {
        $this->surname = $surname;
        $this->firstName = $firstName;
        $this->patronymic = $patronymic;
        $this->gender = $gender;
    }
}
