<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface NameInflectorInterface
{
    public function inflect($name, $case);
    public function detectGender($name);
}
