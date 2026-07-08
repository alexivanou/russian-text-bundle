<?php

namespace AlexIvanou\RussianTextBundle\Service;

use morphos\S;

class AdjectiveDecliner implements AdjectiveDeclinerInterface
{
    private static $hardEndings = array(
        'nominative'    => array('m' => 'ый', 'f' => 'ая', 'n' => 'ое', 'p' => 'ые'),
        'genitive'      => array('m' => 'ого', 'f' => 'ой', 'n' => 'ого', 'p' => 'ых'),
        'dative'        => array('m' => 'ому', 'f' => 'ой', 'n' => 'ому', 'p' => 'ым'),
        'accusative'    => array('m' => 'ого', 'f' => 'ую', 'n' => 'ое', 'p' => 'ых'),
        'ablative'      => array('m' => 'ым', 'f' => 'ой', 'n' => 'ым', 'p' => 'ыми'),
        'prepositional' => array('m' => 'ом', 'f' => 'ой', 'n' => 'ом', 'p' => 'ых'),
    );

    private static $hardStressedEndings = array(
        'nominative'    => array('m' => 'ой', 'f' => 'ая', 'n' => 'ое', 'p' => 'ые'),
        'genitive'      => array('m' => 'ого', 'f' => 'ой', 'n' => 'ого', 'p' => 'ых'),
        'dative'        => array('m' => 'ому', 'f' => 'ой', 'n' => 'ому', 'p' => 'ым'),
        'accusative'    => array('m' => 'ого', 'f' => 'ую', 'n' => 'ое', 'p' => 'ых'),
        'ablative'      => array('m' => 'ым', 'f' => 'ой', 'n' => 'ым', 'p' => 'ыми'),
        'prepositional' => array('m' => 'ом', 'f' => 'ой', 'n' => 'ом', 'p' => 'ых'),
    );

    private static $softEndings = array(
        'nominative'    => array('m' => 'ий', 'f' => 'яя', 'n' => 'ее', 'p' => 'ие'),
        'genitive'      => array('m' => 'его', 'f' => 'ей', 'n' => 'его', 'p' => 'их'),
        'dative'        => array('m' => 'ему', 'f' => 'ей', 'n' => 'ему', 'p' => 'им'),
        'accusative'    => array('m' => 'его', 'f' => 'юю', 'n' => 'ее', 'p' => 'их'),
        'ablative'      => array('m' => 'им', 'f' => 'ей', 'n' => 'им', 'p' => 'ими'),
        'prepositional' => array('m' => 'ем', 'f' => 'ей', 'n' => 'ем', 'p' => 'их'),
    );

    private static $gutturalEndings = array(
        'nominative'    => array('m' => 'ий', 'f' => 'ая', 'n' => 'ое', 'p' => 'ие'),
        'genitive'      => array('m' => 'ого', 'f' => 'ой', 'n' => 'ого', 'p' => 'их'),
        'dative'        => array('m' => 'ому', 'f' => 'ой', 'n' => 'ому', 'p' => 'им'),
        'accusative'    => array('m' => 'ого', 'f' => 'ую', 'n' => 'ое', 'p' => 'их'),
        'ablative'      => array('m' => 'им', 'f' => 'ой', 'n' => 'им', 'p' => 'ими'),
        'prepositional' => array('m' => 'ом', 'f' => 'ой', 'n' => 'ом', 'p' => 'их'),
    );

    public function decline($adjective, $case, $gender = 'm', $animateness = false)
    {
        $lower = S::lower($adjective);
        list($prefix, $type) = $this->parseAdjective($lower);
        $endings = $this->getEndingsForType($type);

        $genderKey = $gender === 'f' ? 'f' : ($gender === 'n' ? 'n' : 'm');
        $caseKey = $this->canonizeCase($case);

        if (!isset($endings[$caseKey][$genderKey])) {
            return $adjective;
        }

        $ending = $endings[$caseKey][$genderKey];
        $prefix = $this->restoreCapitalization($adjective, $prefix);

        return $prefix . $ending;
    }

    public function plural($adjective, $case = 'nominative', $animateness = false)
    {
        $lower = S::lower($adjective);
        list($prefix, $type) = $this->parseAdjective($lower);
        $endings = $this->getEndingsForType($type);

        $caseKey = $this->canonizeCase($case);

        if (!isset($endings[$caseKey]['p'])) {
            return $adjective;
        }

        $prefix = $this->restoreCapitalization($adjective, $prefix);
        return $prefix . $endings[$caseKey]['p'];
    }

    public function getCases($adjective, $gender = 'm', $animateness = false)
    {
        $lower = S::lower($adjective);
        list($prefix, $type) = $this->parseAdjective($lower);
        $endings = $this->getEndingsForType($type);

        $genderKey = $gender === 'f' ? 'f' : ($gender === 'n' ? 'n' : 'm');
        $prefix = $this->restoreCapitalization($adjective, $prefix);

        $result = array();
        foreach (array('nominative', 'genitive', 'dative', 'accusative', 'ablative', 'prepositional') as $caseKey) {
            $result[$caseKey] = $prefix . $endings[$caseKey][$genderKey];
        }
        return $result;
    }

    public function pluralCases($adjective, $animateness = false)
    {
        $lower = S::lower($adjective);
        list($prefix, $type) = $this->parseAdjective($lower);
        $endings = $this->getEndingsForType($type);

        $prefix = $this->restoreCapitalization($adjective, $prefix);

        $result = array();
        foreach (array('nominative', 'genitive', 'dative', 'accusative', 'ablative', 'prepositional') as $caseKey) {
            $result[$caseKey] = $prefix . $endings[$caseKey]['p'];
        }
        return $result;
    }

    private function parseAdjective($adjective)
    {
        $lastTwo = S::slice($adjective, -2);

        if ($lastTwo === 'ой') {
            $prefix = S::slice($adjective, 0, -2);
            return array($prefix, 'hard_stressed');
        }

        if ($lastTwo === 'ый') {
            $prefix = S::slice($adjective, 0, -2);
            return array($prefix, 'hard');
        }

        if ($lastTwo === 'ий') {
            $prefix = S::slice($adjective, 0, -2);
            $before = S::slice($adjective, -3, -2);
            if (in_array($before, array('г', 'к', 'х'))) {
                return array($prefix, 'guttural');
            }
            return array($prefix, 'soft');
        }

        $last = S::slice($adjective, -1);
        if ($last === 'я' || $last === 'е') {
            $prefix = S::slice($adjective, 0, -1);
            return array($prefix, 'soft');
        }

        return array($adjective, 'hard');
    }

    private function getEndingsForType($type)
    {
        switch ($type) {
            case 'hard_stressed':
                return self::$hardStressedEndings;
            case 'soft':
                return self::$softEndings;
            case 'guttural':
                return self::$gutturalEndings;
            default:
                return self::$hardEndings;
        }
    }

    private function restoreCapitalization($original, $prefix)
    {
        if (S::upper(S::slice($original, 0, 1)) === S::slice($original, 0, 1)) {
            return S::upper(S::slice($prefix, 0, 1)) . S::slice($prefix, 1);
        }
        return $prefix;
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
