<?php

namespace AlexIvanou\RussianTextBundle\Service\Validator;

use AlexIvanou\RussianTextBundle\Service\ValidationRuleInterface;

class OkpoValidator implements ValidationRuleInterface
{
    public function getType()
    {
        return 'okpo';
    }

    public function isValid($value)
    {
        if (!preg_match('/^\d{8}$/', $value) && !preg_match('/^\d{10}$/', $value)) {
            return false;
        }

        $digits = array_map('intval', str_split($value));
        $len = count($digits);
        $weights = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);

        $sum = 0;
        for ($i = 0; $i < $len - 1; $i++) {
            $sum += $digits[$i] * $weights[$i];
        }
        $check = $sum % 11 % 10;
        return $check === $digits[$len - 1];
    }
}
