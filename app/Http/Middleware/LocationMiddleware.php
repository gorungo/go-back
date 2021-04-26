<?php
namespace App\Http\Middleware;

use Closure;
use App;
use Request;

class LocationMiddleware
{
    public static $mainCity = 'all'; //основной город, который не должен отображаться в URl

    public static $cities = ['vladivostok', 'phuket', 'bangkok']; // Указываем, какие города будем использовать в приложении.


    /*
     * Проверяет наличие корректной метки языка в текущем URL
     * Возвращает метку или значеие null, если нет метки
     */
    public static function getLocation()
    {
        $uri = Request::path(); //получаем URI

        $request = Request::input();

        $segmentsURI = explode('/',$uri); //делим на части по разделителю "/"

        if(isset($request['location']) && $request['location'] != null){

            // если есть параметр location, то еспользуем его, вместо параметра из строки
            return $request['location'];

        }else{

            //Проверяем метку языка  - есть ли она среди доступных языков
            if (!empty($segmentsURI[1]) && in_array($segmentsURI[1], self::$cities)) {

                if ($segmentsURI[1] != self::$mainCity) return $segmentsURI[1];

            } elseif(!empty($segmentsURI[0]) && in_array($segmentsURI[0], self::$cities)) {

                if ($segmentsURI[0] != self::$mainCity) return $segmentsURI[0];
            }
        }


        return null;
    }

    /*
    * Устанавливает язык приложения в зависимости от метки языка из URL
    */
    public function handle($request, Closure $next)
    {
        //$locale = self::getLocation();

        //if($locale) App::setLocation($locale);
        //если метки нет - устанавливаем основной язык $mainLanguage
        //else App::setLocale(self::$mainLanguage);

        return $next($request); //пропускаем дальше - передаем в следующий посредник
    }

}