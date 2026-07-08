<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface GeographicalNameInflectorInterface
{
    public function inflect($name, $case);
}
