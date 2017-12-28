<?php

/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 27.12.2017
 * Time: 16:53
 */
class CommonHelper
{
    public static function translit($text, $maxLength = 64, $toLowCase = true)
    {
        $text = (string)$text; // преобразуем в строковое значение
        $text = strip_tags($text); // убираем HTML-теги
        $text = str_replace(array("\n", "\r"), " ", $text); // убираем перевод каретки
        $text = preg_replace("/\s+/", ' ', $text); // удаляем повторяющие пробелы
        $text = trim($text); // убираем пробелы в начале и конце строки
        $text = function_exists('mb_strtolower') ? mb_strtolower($text) : strtolower($text); // переводим строку в нижний регистр (иногда надо задать локаль)
        $text = strtr($text, [
            'а'=>'a',
            'б'=>'b',
            'в'=>'v',
            'г'=>'g',
            'д'=>'d',
            'е'=>'e',
            'ё'=>'e',
            'ж'=>'j',
            'з'=>'z',
            'и'=>'i',
            'й'=>'y',
            'к'=>'k',
            'л'=>'l',
            'м'=>'m',
            'н'=>'n',
            'о'=>'o',
            'п'=>'p',
            'р'=>'r',
            'с'=>'s',
            'т'=>'t',
            'у'=>'u',
            'ф'=>'f',
            'х'=>'h',
            'ц'=>'c',
            'ч'=>'ch',
            'ш'=>'sh',
            'щ'=>'shch',
            'ы'=>'y',
            'э'=>'e',
            'ю'=>'yu',
            'я'=>'ya',
            'ъ'=>'',
            'ь'=>''
            ]
        );
        $text = preg_replace("/[^0-9a-z-_ ]/i", "", $text); // очищаем строку от недопустимых символов
        $text = str_replace(" ", "-", $text); // заменяем пробелы знаком минус

        $text = mb_substr($text, 0, $maxLength);
        if ($toLowCase) {
            $text = mb_strtolower($text);
        }

        return trim($text, '-');
    }
}