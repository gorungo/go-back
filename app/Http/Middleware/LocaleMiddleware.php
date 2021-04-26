<?php
namespace App\Http\Middleware;

use Closure;
use App;
use Illuminate\Support\Facades\Log;
use Request;
use App\Locale;
use Illuminate\Support\Facades\Redirect;

class LocaleMiddleware
{

    public function __construct()
    {

    }

    /*
     * Проверяет наличие корректной метки языка в текущем URL
     * Возвращает метку или значеие null, если нет метки
     */
    public static function getLocale()
    {
        $locale = Request()->input('locale');
        if($locale){
            if(in_array($locale, config('app.languages'))){
                // if we have get param locale
                return $locale !== config('app.locale') ? $locale : null;
            }
        }else{
            $segmentsURI = explode('/', Request()->url()); //делим на части по разделителю "/"

            //Проверяем метку языка  - есть ли она среди доступных языков
            if (!empty($segmentsURI[0]) && in_array($segmentsURI[0], config('app.languages'))) {
                if ($segmentsURI[0] != config('app.locale')) return $segmentsURI[0];
            }

        }



        return null;
    }

    /*
 * Проверяет наличие корректной метки языка в текущем URL
 * Возвращает id метки или значеие 1, если нет метки (значит дифолтный локэил)
 */
    public static function getLocaleId($localeName = '')
    {
        if($localeName === '') {
            $locale = self::getLocale();
        }else{
            $locale = $localeName;
        }

        if($locale){
            $key = array_search($locale, config('app.languages'));

            if($key !== Null){
                return $key + 1;
            }
        }else{
            $key = array_search(config('app.locale'), config('app.languages'));

            if($key !== Null){
                return $key + 1;
            }
        };

        return 1;
    }

    public function isValidLocaleSymbol($localeSymbol){
        return in_array($localeSymbol, config('app.languages'));
    }

    /**
    * Устанавливает язык приложения в зависимости от метки языка из URL
    */
    public function handle($request, Closure $next)
    {
        $locale = self::getLocale();

        if($locale) App::setLocale($locale);
        //если метки нет - устанавливаем основной язык $mainLanguage
        else App::setLocale(config('app.locale'));

        return $next($request); //пропускаем дальше - передаем в следующий посредник
    }

    /**
     * Имя пользовательской локали из запроса
     */
    public function getUserLanguageFromRequest(){
        // parsing string like this 'en-GB,en;q=0.8'
        return explode(explode(request()->server('HTTP_ACCEPT_LANGUAGE'), ';')[0],',')[0];
    }


}
