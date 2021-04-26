<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\SocialAccountService;

use Socialite;

use Illuminate\Support\Facades\Log;

class SocialController extends Controller
{
    /**
     * Redirect the user to the Provider authentication page.
     *
     * @param string $provider
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        try {

            return Socialite::driver($provider)->redirect();

        }catch(\Exception $e){

            log::error($e);
            return redirect()->back()->with('status', ['В данный момент вход ч-з соц. сети недоступен. Повторите попытку позже.','red']);
        }
    }

    /**
     * Obtain the user information from Provider.
     *
     * @param SocialAccountService $service
     * @param string $provider
     * @return Response
     */
    public function handleProviderCallback(SocialAccountService $service, $provider)
    {

        $result = [];

        try{

            $driver = Socialite::driver($provider);

            // создаем или получаем существующего пользователя на основе данных от соц. сети
            $user = $service->createOrGetUser($driver, $provider);

            if($user){
                \Auth::login($user, true);
                return redirect()->intended('/user/' . Auth()->User()->id . '/profile');

            }else{
                log::error('Ошибка при входе ч-з соц. сеть. SocialController->handleProviderCallback');
                return redirect()->intended('/login')->with('status', ['В данный момент вход ч-з соц. сети недоступен. Повторите попытку позже.','red']);

            }

        }catch( \Exception $e){

            log::error($e);

            // если была ошибка при получении инфы от социальной сети
            return redirect()->intended('/login');
        }




    }
}
