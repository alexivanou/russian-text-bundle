<?php

namespace AlexIvanou\RussianTextBundle\Service\Validator;

use AlexIvanou\RussianTextBundle\Service\ValidationRuleInterface;

class UnpValidator implements ValidationRuleInterface
{
    public function getType()
    {
        return 'unp';
    }

    public function isValid($value)
    {
        if (!preg_match('/^\d{9}$/', $value)) {
            return false;
        }

        $digits = array_map('intval', str_split($value));
        $weights = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);
        $sum = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += $digits[$i] * $weights[$i];
        }
        $check = $sum % 11;
        if ($check === 10) {
            $sum = 0;
            for ($i = 0; $i < 8; $i++) {
                $sum += $digits[$i] * ($weights[$i] + 2);
            }
            $check = $sum % 11 % 10;
        }
        return $check === $digits[8];
    }
}
