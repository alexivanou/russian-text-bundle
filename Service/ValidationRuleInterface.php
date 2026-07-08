<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface ValidationRuleInterface
{
    public function getType();
    public function isValid($value);
}
