<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface NumberSpellerInterface
{
    public function cardinal($number, $gender = 'm');
    public function ordinal($number, $gender = 'm');
}
