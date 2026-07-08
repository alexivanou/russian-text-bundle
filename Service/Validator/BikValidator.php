<?php

namespace AlexIvanou\RussianTextBundle\Service\Validator;

use AlexIvanou\RussianTextBundle\Service\ValidationRuleInterface;

class BikValidator implements ValidationRuleInterface
{
    public function getType()
    {
        return 'bik';
    }

    public function isValid($value)
    {
        return (bool) preg_match('/^\d{9}$/', $value);
    }
}
