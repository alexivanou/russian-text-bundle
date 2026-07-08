<?php

namespace AlexIvanou\RussianTextBundle\Service;

use morphos\S;

class Transliterator implements TransliteratorInterface
{
    private static $isoMap = array(
        'А' => 'A', 'а' => 'a',
        'Б' => 'B', 'б' => 'b',
        'В' => 'V', 'в' => 'v',
        'Г' => 'G', 'г' => 'g',
        'Д' => 'D', 'д' => 'd',
        'Е' => 'E', 'е' => 'e',
        'Ё' => 'Ë', 'ё' => 'ë',
        'Ж' => 'Ž', 'ж' => 'ž',
        'З' => 'Z', 'з' => 'z',
        'И' => 'I', 'и' => 'i',
        'Й' => 'J', 'й' => 'j',
        'К' => 'K', 'к' => 'k',
        'Л' => 'L', 'л' => 'l',
        'М' => 'M', 'м' => 'm',
        'Н' => 'N', 'н' => 'n',
        'О' => 'O', 'о' => 'o',
        'П' => 'P', 'п' => 'p',
        'Р' => 'R', 'р' => 'r',
        'С' => 'S', 'с' => 's',
        'Т' => 'T', 'т' => 't',
        'У' => 'U', 'у' => 'u',
        'Ф' => 'F', 'ф' => 'f',
        'Х' => 'H', 'х' => 'h',
        'Ц' => 'C', 'ц' => 'c',
        'Ч' => 'Č', 'ч' => 'č',
        'Ш' => 'Š', 'ш' => 'š',
        'Щ' => 'Ŝ', 'щ' => 'ŝ',
        'Ъ' => 'ʺ', 'ъ' => 'ʺ',
        'Ы' => 'Y', 'ы' => 'y',
        'Ь' => 'ʹ', 'ь' => 'ʹ',
        'Э' => 'È', 'э' => 'è',
        'Ю' => 'Û', 'ю' => 'û',
        'Я' => 'Â', 'я' => 'â',
        'І' => 'Ì', 'і' => 'ì',
        'Ї' => 'Ï', 'ї' => 'ï',
        'Є' => 'Ê', 'є' => 'ê',
        'Ґ' => 'G̀', 'ґ' => 'g̀',
        'Ў' => 'Ŭ', 'ў' => 'ŭ',
    );

    private static $slugMap = array(
        'А' => 'A', 'а' => 'a',
        'Б' => 'B', 'б' => 'b',
        'В' => 'V', 'в' => 'v',
        'Г' => 'G', 'г' => 'g',
        'Д' => 'D', 'д' => 'd',
        'Е' => 'E', 'е' => 'e',
        'Ё' => 'Yo', 'ё' => 'yo',
        'Ж' => 'Zh', 'ж' => 'zh',
        'З' => 'Z', 'з' => 'z',
        'И' => 'I', 'и' => 'i',
        'Й' => 'Y', 'й' => 'y',
        'К' => 'K', 'к' => 'k',
        'Л' => 'L', 'л' => 'l',
        'М' => 'M', 'м' => 'm',
        'Н' => 'N', 'н' => 'n',
        'О' => 'O', 'о' => 'o',
        'П' => 'P', 'п' => 'p',
        'Р' => 'R', 'р' => 'r',
        'С' => 'S', 'с' => 's',
        'Т' => 'T', 'т' => 't',
        'У' => 'U', 'у' => 'u',
        'Ф' => 'F', 'ф' => 'f',
        'Х' => 'Kh', 'х' => 'kh',
        'Ц' => 'Ts', 'ц' => 'ts',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',
        'Щ' => 'Shch', 'щ' => 'shch',
        'Ъ' => '', 'ъ' => '',
        'Ы' => 'Y', 'ы' => 'y',
        'Ь' => '', 'ь' => '',
        'Э' => 'E', 'э' => 'e',
        'Ю' => 'Yu', 'ю' => 'yu',
        'Я' => 'Ya', 'я' => 'ya',
        'І' => 'I', 'і' => 'i',
        'Ї' => 'Yi', 'ї' => 'yi',
        'Є' => 'Ye', 'є' => 'ye',
        'Ґ' => 'G', 'ґ' => 'g',
        'Ў' => 'U', 'ў' => 'u',
    );

    private static $latinAliases = array(
        'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ñ' => 'n',
        'ç' => 'c', 'ł' => 'l', 'đ' => 'd',
    );

    public function translit($text, $iso = false)
    {
        $map = $iso ? self::$isoMap : self::$slugMap;
        $result = '';

        $length = S::length($text);
        for ($i = 0; $i < $length; $i++) {
            $char = S::slice($text, $i, $i + 1);
            if (isset($map[$char])) {
                $result .= $map[$char];
            } elseif (isset(self::$latinAliases[$char])) {
                $result .= self::$latinAliases[$char];
            } else {
                $result .= $char;
            }
        }

        return $result;
    }

    public function slug($text, $separator = '-')
    {
        $result = $this->translit($text, false);
        $result = preg_replace('/[^\w\s\-]/u', '', $result);
        $result = preg_replace('/[\s\-]+/u', $separator, trim($result));
        $result = trim($result, $separator);
        return mb_strtolower($result);
    }
}
