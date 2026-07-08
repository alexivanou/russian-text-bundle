<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface AdjectiveDeclinerInterface
{
    public function decline($word, $case, $gender = 'm', $number = 'singular');
}
