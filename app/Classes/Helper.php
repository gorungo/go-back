<?php

namespace App\Classes;


class Helper
{
    public static function rDate($param, $time = 0)
    {
        if (intval($time) == 0) $time = time();
        $MonthNames = array("Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря");

        if (strpos($param, 'M') === false) return date($param, $time);
        else return date(str_replace('M', $MonthNames[date('n', $time) - 1], $param), $time);
    }

    public static function dayOfWeek($dayNum){
        $days = array(
            'Воскресенье' , 'Понедельник' ,
            'Вторник' , 'Среда' ,
            'Четверг' , 'Пятница' , 'Суббота'
        );
        return $days[$dayNum];
    }

    public static function dayOfWeekShort($dayNum){
        $days = array(
            'ВС' , 'ПН' ,
            'ВТ' , 'СР' ,
            'ЧТ' , 'ПТ' , 'СБ'
        );
        return $days[$dayNum];
    }

    public static function clearPhone($phone)
    {
        $phone = str_replace(["+7"], "7", $phone);
        return str_replace(["-", "(", ")", " ", "+"], "", $phone);
    }
}
