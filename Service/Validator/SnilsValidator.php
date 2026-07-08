<?php

namespace AlexIvanou\RussianTextBundle\Service\Validator;

use AlexIvanou\RussianTextBundle\Service\ValidationRuleInterface;

class SnilsValidator implements ValidationRuleInterface
{
    public function getType()
    {
        return 'snils';
    }

    public function isValid($value)
    {
        if (!preg_match('/^\d{11}$/', $value)) {
            return false;
        }

        $digits = array_map('intval', str_split($value));
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $digits[$i] * (9 - $i);
        }
        $check = $sum < 100 ? $sum : ($sum % 101 === 100 ? 0 : $sum % 101);

        return $check === (int) substr($value, -2);
    }
}
