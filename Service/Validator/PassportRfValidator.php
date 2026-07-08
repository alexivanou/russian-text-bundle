<?php

namespace AlexIvanou\RussianTextBundle\Service\Validator;

use AlexIvanou\RussianTextBundle\Service\ValidationRuleInterface;

class PassportRfValidator implements ValidationRuleInterface
{
    public function getType()
    {
        return 'passport_rf';
    }

    public function isValid($value)
    {
        if (!preg_match('/^\d{10}$/', $value)) {
            return false;
        }
        $series = (int) substr($value, 0, 2);
        return $series >= 1 && $series <= 99;
    }
}
