<?php

namespace AlexIvanou\RussianTextBundle\Service\Validator;

use AlexIvanou\RussianTextBundle\Service\ValidationRuleInterface;

class InnValidator implements ValidationRuleInterface
{
    public function getType()
    {
        return 'inn';
    }

    public function isValid($value)
    {
        if (!preg_match('/^\d{10}$/', $value) && !preg_match('/^\d{12}$/', $value)) {
            return false;
        }

        $digits = array_map('intval', str_split($value));
        $len = count($digits);

        if ($len === 10) {
            $weights = array(2, 4, 10, 3, 5, 9, 4, 6, 8, 0);
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                $sum += $digits[$i] * $weights[$i];
            }
            return ($sum % 11) % 10 === $digits[9];
        }

        $weights1 = array(7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0);
        $sum = 0;
        for ($i = 0; $i < 11; $i++) {
            $sum += $digits[$i] * $weights1[$i];
        }
        $check1 = ($sum % 11) % 10;
        if ($check1 !== $digits[10]) {
            return false;
        }

        $weights2 = array(3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0);
        $sum = 0;
        for ($i = 0; $i < 11; $i++) {
            $sum += $digits[$i] * $weights2[$i];
        }
        $check2 = ($sum % 11) % 10;
        return $check2 === $digits[11];
    }
}
