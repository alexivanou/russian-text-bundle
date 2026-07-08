<?php

namespace AlexIvanou\RussianTextBundle\Service;

class PhoneFormatter implements PhoneFormatterInterface
{
    private static $countries = array(
        'RU' => array('name' => 'Россия', 'code' => '7', 'national_len' => 10, 'mask' => '+7 (###) ###-##-##'),
        'KZ' => array('name' => 'Казахстан', 'code' => '7', 'national_len' => 10, 'mask' => '+7 (###) ###-##-##'),
        'UA' => array('name' => 'Украина', 'code' => '380', 'national_len' => 9, 'mask' => '+380 (##) ###-##-##'),
        'BY' => array('name' => 'Беларусь', 'code' => '375', 'national_len' => 9, 'mask' => '+375 (##) ###-##-##'),
        'UZ' => array('name' => 'Узбекистан', 'code' => '998', 'national_len' => 9, 'mask' => '+998 (##) ###-##-##'),
        'AZ' => array('name' => 'Азербайджан', 'code' => '994', 'national_len' => 9, 'mask' => '+994 (##) ###-##-##'),
        'AM' => array('name' => 'Армения', 'code' => '374', 'national_len' => 8, 'mask' => '+374 (##) ###-###'),
        'KG' => array('name' => 'Кыргызстан', 'code' => '996', 'national_len' => 9, 'mask' => '+996 (###) ###-###'),
        'TJ' => array('name' => 'Таджикистан', 'code' => '992', 'national_len' => 9, 'mask' => '+992 (##) ###-##-##'),
        'TM' => array('name' => 'Туркменистан', 'code' => '993', 'national_len' => 8, 'mask' => '+993 (##) ###-###'),
        'MD' => array('name' => 'Молдова', 'code' => '373', 'national_len' => 8, 'mask' => '+373 (##) ###-###'),
        'LT' => array('name' => 'Литва', 'code' => '370', 'national_len' => 8, 'mask' => '+370 (##) ###-###'),
        'LV' => array('name' => 'Латвия', 'code' => '371', 'national_len' => 8, 'mask' => '+371 (##) ###-###'),
        'EE' => array('name' => 'Эстония', 'code' => '372', 'national_len' => 7, 'mask' => '+372 (###) ####'),
        'DE' => array('name' => 'Германия', 'code' => '49', 'national_len' => 10, 'mask' => '+49 (###) ###-###-##'),
        'FR' => array('name' => 'Франция', 'code' => '33', 'national_len' => 9, 'mask' => '+33 (###) ###-###'),
        'IT' => array('name' => 'Италия', 'code' => '39', 'national_len' => 9, 'mask' => '+39 (###) ###-####'),
        'ES' => array('name' => 'Испания', 'code' => '34', 'national_len' => 9, 'mask' => '+34 (###) ###-###'),
        'UK' => array('name' => 'Великобритания', 'code' => '44', 'national_len' => 10, 'mask' => '+44 (####) ######'),
        'PL' => array('name' => 'Польша', 'code' => '48', 'national_len' => 9, 'mask' => '+48 (###) ###-###'),
        'CZ' => array('name' => 'Чехия', 'code' => '420', 'national_len' => 9, 'mask' => '+420 (###) ###-###'),
        'NL' => array('name' => 'Нидерланды', 'code' => '31', 'national_len' => 9, 'mask' => '+31 (##) ###-####'),
        'AT' => array('name' => 'Австрия', 'code' => '43', 'national_len' => 9, 'mask' => '+43 (###) ###-####'),
        'CH' => array('name' => 'Швейцария', 'code' => '41', 'national_len' => 9, 'mask' => '+41 (##) ###-####'),
        'US' => array('name' => 'США', 'code' => '1', 'national_len' => 10, 'mask' => '+1 (###) ###-####'),
        'CA' => array('name' => 'Канада', 'code' => '1', 'national_len' => 10, 'mask' => '+1 (###) ###-####'),
        'CN' => array('name' => 'Китай', 'code' => '86', 'national_len' => 11, 'mask' => '+86 (###) ####-####'),
        'TR' => array('name' => 'Турция', 'code' => '90', 'national_len' => 10, 'mask' => '+90 (###) ###-##-##'),
        'IL' => array('name' => 'Израиль', 'code' => '972', 'national_len' => 9, 'mask' => '+972 (##) ###-####'),
        'IN' => array('name' => 'Индия', 'code' => '91', 'national_len' => 10, 'mask' => '+91 (####) ###-###'),
    );

    public function format($phone, $countryCode = null)
    {
        $digits = preg_replace('/[^\d]/', '', $phone);

        if ($countryCode !== null && isset(self::$countries[$countryCode])) {
            return $this->formatByCountry($digits, $countryCode);
        }

        $country = $this->detectCountry($digits);
        if ($country !== null) {
            return $this->formatByCountry($digits, $country);
        }

        return '+' . $digits;
    }

    public function isValid($phone, $countryCode = null)
    {
        $digits = preg_replace('/[^\d]/', '', $phone);

        if ($countryCode !== null && isset(self::$countries[$countryCode])) {
            $info = self::$countries[$countryCode];
            $len = $this->getNationalLength($digits, $info);
            return $len === $info['national_len'];
        }

        $country = $this->detectCountry($digits);
        return $country !== null;
    }

    public function detectCountry($phone)
    {
        $digits = preg_replace('/[^\d]/', '', $phone);

        $candidates = array();
        foreach (self::$countries as $code => $info) {
            $countryLen = strlen($info['code']);
            $prefix = substr($digits, 0, $countryLen);
            $codeLen = strlen($info['code']);
            $prefix2 = strlen($info['code']);
            if (substr($digits, 0, strlen($info['code'])) === $info['code']) {
                $national = substr($digits, strlen($info['code']));
                if (strlen($national) === $info['national_len']) {
                    $candidates[] = $code;
                }
            }
        }

        if (count($candidates) === 1) {
            return $candidates[0];
        }

        if (count($candidates) > 1) {
            return $candidates[0];
        }

        return null;
    }

    public function getCountryCode($countryCode)
    {
        if (isset(self::$countries[$countryCode])) {
            return '+' . self::$countries[$countryCode]['code'];
        }
        return null;
    }

    public function getCountryInfo($countryCode)
    {
        if (isset(self::$countries[$countryCode])) {
            return self::$countries[$countryCode];
        }
        return null;
    }

    public function getSupportedCountries()
    {
        return array_keys(self::$countries);
    }

    private function formatByCountry($digits, $countryCode)
    {
        $info = self::$countries[$countryCode];
        $national = $this->getNationalDigits($digits, $info);

        if ($national === null) {
            return '+' . $digits;
        }

        return $this->applyMask($national, $info['mask']);
    }

    private function getNationalDigits($digits, $info)
    {
        $codeLen = strlen($info['code']);
        if (strlen($digits) >= $codeLen && substr($digits, 0, $codeLen) === $info['code']) {
            $national = substr($digits, $codeLen);
            if (strlen($national) === $info['national_len']) {
                return $national;
            }
        }
        if (strlen($digits) >= $codeLen && substr($digits, 0, $codeLen) === $info['code']) {
            return null;
        }

        if (strlen($digits) === $info['national_len']) {
            return $digits;
        }

        return null;
    }

    private function getNationalLength($digits, $info)
    {
        $codeLen = strlen($info['code']);
        if (strlen($digits) >= $codeLen && substr($digits, 0, $codeLen) === $info['code']) {
            return strlen($digits) - $codeLen;
        }
        return strlen($digits);
    }

    private function applyMask($digits, $mask)
    {
        $result = '';
        $digitIndex = 0;
        for ($i = 0; $i < strlen($mask); $i++) {
            if ($mask[$i] === '#' && $digitIndex < strlen($digits)) {
                $result .= $digits[$digitIndex];
                $digitIndex++;
            } else {
                $result .= $mask[$i];
            }
        }
        return $result;
    }
}
