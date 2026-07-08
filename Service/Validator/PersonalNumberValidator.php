<?php

namespace AlexIvanou\RussianTextBundle\Service\Validator;

use AlexIvanou\RussianTextBundle\Service\ValidationRuleInterface;

class PersonalNumberValidator implements ValidationRuleInterface
{
    public function getType()
    {
        return 'personal_number';
    }

    public function isValid($value)
    {
        return (bool) preg_match('/^[A-Z0-9]{14}$/', strtoupper($value));
    }
}
