<?php

namespace AlexIvanou\RussianTextBundle\Service\Validator;

use AlexIvanou\RussianTextBundle\Service\ValidationRuleInterface;

class KppValidator implements ValidationRuleInterface
{
    public function getType()
    {
        return 'kpp';
    }

    public function isValid($value)
    {
        return (bool) preg_match('/^\d{4}[\dA-Z]{2}\d{3}$/', $value);
    }
}
