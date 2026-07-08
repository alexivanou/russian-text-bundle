<?php

namespace AlexIvanou\RussianTextBundle\Service\Validator;

use AlexIvanou\RussianTextBundle\Service\ValidationRuleInterface;

class PassportRbValidator implements ValidationRuleInterface
{
    public function getType()
    {
        return 'passport_rb';
    }

    public function isValid($value)
    {
        return (bool) preg_match('/^[A-Z]{2}\d{7}$/', strtoupper($value));
    }
}
