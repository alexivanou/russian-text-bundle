<?php

namespace AlexIvanou\RussianTextBundle\Service;

use morphos\S;

class GeographicalNameInflector implements GeographicalNameInflectorInterface
{
    private static $immutable = array(
        'сочи', 'баку', 'тбилиси', 'осло', 'токио', 'киото',
        'дели', 'кали', 'чикаго', 'бордо', 'сухуми', 'цхинвали',
        'гоби', 'конго', 'миссисипи', 'онтарио', 'юкон',
    );

    private $nounDecliner;

    public function __construct()
    {
        $this->nounDecliner = new NounDecliner();
    }

    public function inflect($name, $case)
    {
        $name = trim($name);
        $lower = S::lower($name);

        if (in_array($lower, self::$immutable)) {
            return $name;
        }

        if ($this->isCompound($lower, $name)) {
            return $this->inflectCompound($name, $case);
        }

        if ($this->isOvCity($lower)) {
            return $this->inflectOvCity($name, $lower, $case);
        }

        if ($this->isSkCity($lower)) {
            return $this->inflectSkCity($name, $lower, $case);
        }

        $result = $this->nounDecliner->decline($lower, $case);
        return $this->restoreCapitalization($name, $result);
    }

    public function getCases($name)
    {
        $name = trim($name);
        $lower = S::lower($name);

        if (in_array($lower, self::$immutable)) {
            $cases = array();
            foreach (array('nominative', 'genitive', 'dative', 'accusative', 'ablative', 'prepositional') as $c) {
                $cases[$c] = $name;
            }
            return $cases;
        }

        $cases = array();
        foreach (array('nominative', 'genitive', 'dative', 'accusative', 'ablative', 'prepositional') as $c) {
            $cases[$c] = $this->inflect($name, $c);
        }
        return $cases;
    }

    public function isMutable($name)
    {
        return !in_array(S::lower(trim($name)), self::$immutable);
    }

    private function isCompound($lower, $name)
    {
        $parts = explode(' ', $name);
        if (count($parts) < 2) {
            return false;
        }

        $compoundPrefixes = array(
            'нижний', 'верхний', 'великий', 'старый', 'новый',
            'малый', 'большой', 'каменный', 'верхняя', 'нижняя',
            'нижнее', 'верхнее', 'северный', 'южный', 'западный',
            'восточный', 'центральный',
        );

        return in_array(S::lower($parts[0]), $compoundPrefixes);
    }

    private function isOvCity($lower)
    {
        $lastTwo = S::slice($lower, -2);
        if (in_array($lastTwo, array('ов', 'ев', 'ин', 'ын'))) {
            $lastThree = S::slice($lower, -3);
            if (in_array($lastThree, array('ово', 'ево', 'ино', 'ыно'))) {
                return false;
            }
            return true;
        }
        return false;
    }

    private function isSkCity($lower)
    {
        $lastThree = S::slice($lower, -3);
        if ($lastThree === 'скк') {
            return false;
        }
        $lastTwo = S::slice($lower, -2);
        return $lastTwo === 'ск' && S::length($lower) > 3;
    }

    private function inflectOvCity($name, $lower, $case)
    {
        $prefix = S::slice($name, 0, -2);
        $lastTwo = S::slice($lower, -2);
        $firstLetter = S::slice($lastTwo, 0, 1);

        $isYн = $lastTwo === 'ын';
        $endings = array(
            'nominative'    => $lastTwo,
            'genitive'      => $isYн ? 'ына' : $firstLetter . 'ва',
            'dative'        => $isYн ? 'ыну' : $firstLetter . 'ву',
            'accusative'    => $lastTwo,
            'ablative'      => $isYн ? 'ыном' : $firstLetter . 'вом',
            'prepositional' => $isYн ? 'ыне' : $firstLetter . 'ве',
        );

        $caseKey = $this->canonizeCase($case);
        if (isset($endings[$caseKey])) {
            return $prefix . $endings[$caseKey];
        }

        $result = $this->nounDecliner->decline($lower, $case);
        return $this->restoreCapitalization($name, $result);
    }

    private function inflectSkCity($name, $lower, $case)
    {
        $prefix = S::slice($name, 0, -2);

        $endings = array(
            'nominative'    => 'ск',
            'genitive'      => 'ска',
            'dative'        => 'ску',
            'accusative'    => 'ск',
            'ablative'      => 'ском',
            'prepositional' => 'ске',
        );

        $caseKey = $this->canonizeCase($case);
        if (isset($endings[$caseKey])) {
            return $prefix . $endings[$caseKey];
        }

        $result = $this->nounDecliner->decline($lower, $case);
        return $this->restoreCapitalization($name, $result);
    }

    private function inflectCompound($name, $case)
    {
        $parts = explode(' ', $name, 2);
        if (count($parts) < 2) {
            return $this->inflect($name, $case);
        }

        list($first, $second) = $parts;

        $lower = S::lower($first);
        $adjectivePrefixes = array(
            'нижний', 'верхний', 'великий', 'старый', 'новый',
            'малый', 'большой', 'каменный',
        );

        if (in_array($lower, $adjectivePrefixes)) {
            $adjDecliner = new AdjectiveDecliner();
            $firstInflected = $adjDecliner->decline($first, $case, 'm', true);
        } else {
            $firstInflected = $this->inflect($first, $case);
        }

        $secondInflected = $this->inflect($second, $case);

        return $firstInflected . ' ' . $secondInflected;
    }

    private function restoreCapitalization($original, $result)
    {
        $firstChar = S::slice($original, 0, 1);
        $resultFirst = S::slice($result, 0, 1);
        if ($firstChar !== $resultFirst && S::upper($firstChar) === S::upper($resultFirst)) {
            if ($firstChar === S::upper($firstChar)) {
                return S::upper(S::slice($result, 0, 1)) . S::slice($result, 1);
            }
        }
        return $result;
    }

    private function canonizeCase($case)
    {
        $case = S::lower($case);
        switch ($case) {
            case 'nominative':
            case 'именительный':
            case 'и':
                return 'nominative';
            case 'genitive':
            case 'родительный':
            case 'р':
                return 'genitive';
            case 'dative':
            case 'дательный':
            case 'д':
                return 'dative';
            case 'accusative':
            case 'винительный':
            case 'в':
                return 'accusative';
            case 'ablative':
            case 'творительный':
            case 'т':
                return 'ablative';
            case 'prepositional':
            case 'предложный':
            case 'п':
                return 'prepositional';
            default:
                return 'nominative';
        }
    }
}
