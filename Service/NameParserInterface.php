<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface NameParserInterface
{
    public function parse($fullName);
    public function initials($fullName, $style = 'after');
}
