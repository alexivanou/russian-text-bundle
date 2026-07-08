<?php

namespace AlexIvanou\RussianTextBundle\Service\Validator;

use AlexIvanou\RussianTextBundle\Service\ValidationRuleInterface;

class OgrnValidator implements ValidationRuleInterface
{
    public function getType()
    {
        return 'ogrn';
    }

    public function isValid($value)
    {
        if (!preg_match('/^\d{13}$/', $value) && !preg_match('/^\d{15}$/', $value)) {
            return false;
        }

        $digits = array_map('intval', str_split($value));
        $len = count($digits);

        if ($len === 13) {
            $check = (int) substr($value, 0, -1) % 11 % 10;
            return $check === $digits[12];
        }

        $check = (int) substr($value, 0, -1) % 13 % 10;
        return $check === $digits[14];
    }
}
