<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface TransliteratorInterface
{
    public function translit($text, $iso = false);
    public function slug($text, $separator = '-');
}
