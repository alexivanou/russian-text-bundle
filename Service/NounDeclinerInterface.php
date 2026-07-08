<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface NounDeclinerInterface
{
    public function decline($word, $case);
    public function pluralize($word, $count = 2);
}
