<?php

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

    public static function getCreditTerms($from = null, $to = null)
    {
        $timeArr = [
            '1' => '1 месяц',
            '3' => '3 месяца',
            '6' => '6 месяцев',
            '9' => '9 месяцев',
            '12' => '1 год',
            '18' => '1,5 года',
            '24' => '2 года',
            '36' => '3 года',
            '48' => '4 года',
            '60' => '5 лет',
            '72' => '6 лет',
            '84' => '7 лет',
            '120' => '10 лет',
            '180' => '15 лет',
            '240' => '20 лет',
            '300' => '25 лет',
            '360' => '30 лет',
        ];

        $new_arr = [];
        if ($from != null && $to != null) {
            foreach ($timeArr as $key => $value) {
                if ($key >= $from) {
                    $new_arr[$key] = $value;
                }
                if ($key == $to) break;
            }
            return $new_arr;
        } elseif ($from != null) {
            foreach ($timeArr as $key => $value) {
                if ($key >= $from) {
                    $new_arr[$key] = $value;
                }
            }
            return $new_arr;
        } elseif ($to != null) {
            foreach ($timeArr as $key => $value) {
                $new_arr[$key] = $value;
                if ($key == $to) break;
            }
            return $new_arr;
        } else {
            return $timeArr;
        }
    }

    public static function getFastFilters($productName = 'credit')
    {
        $fastFilters = \App\FastFilter::where('product_type', $productName)->
                                        where('is_approved', true)->orderBy('sort_order')->get();

//        foreach ($fastFilters as $ff){
//            $ff->url =
//        }

//        $fastFilters = $this->em->getRepository('AppBundle:FastFilter')->findBy([
//            'product' => $this->em->getRepository('AppBundle:Product')->findOneByName($productName),
//            'approved' => 1,
//        ], ['sortOrder' => 'ASC']);

        return $fastFilters;

    }
}