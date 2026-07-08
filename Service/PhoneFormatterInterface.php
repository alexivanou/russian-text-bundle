<?php

namespace AlexIvanou\RussianTextBundle\Service;

interface PhoneFormatterInterface
{
    public function format($phone, $countryCode = null);
    public function isValid($phone, $countryCode = null);
    public function detectCountry($phone);
}
