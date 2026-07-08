<?php

namespace AlexIvanou\RussianTextBundle\Service\Validator;

use AlexIvanou\RussianTextBundle\Service\ValidationRuleInterface;

class SwiftValidator implements ValidationRuleInterface
{
    public function getType()
    {
        return 'swift';
    }

    public function isValid($value)
    {
        return (bool) preg_match('/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/', strtoupper($value));
    }
}
