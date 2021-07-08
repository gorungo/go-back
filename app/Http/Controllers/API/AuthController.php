<?php

namespace App\Http\Controllers\API;

use App\Classes\Helper;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendVerificationCodeRequest;
use App\Http\Requests\Auth\CheckVerificationCodeRequest;

use App\Models\User;
use App\Services\PhoneVerificationService;

use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\PhoneVerification as PhoneVerificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
            'login', 'register', 'sendVerificationCode', 'checkVerificationCode'
        ]]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  LoginRequest  $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);
        if ( !$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'invite' => 'required|string|min:3|max:100',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::register($request->only(['email', 'password', 'invite']));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => new UserResource($user)
        ], 201);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(new UserResource(auth()->user()));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Send phone validation sms.
     *
     * @param  SendVerificationCodeRequest  $request
     * @return JsonResponse
     */
    public function sendVerificationCode(SendVerificationCodeRequest $request): JsonResponse
    {
        $pvs = new PhoneVerificationService();
        $pvs->test = false;

        $activeVerification = $pvs->phoneActiveVerification($request->input('data.phone'));

        if($activeVerification){
            return response()->json([
                'type' => 'old',
                'phone_verification' => new PhoneVerificationResource($activeVerification)
            ]);
        }

        if($verification = $pvs->createVerificationAndSendCode($request->input('data.phone'))){
            return response()->json([
                'type' => 'new',
                'phone_verification' => new PhoneVerificationResource($verification)
            ]);
        }

        return response()->json([
            'phone_verification' => null,
            'type' => 'error',
            'message' => 'service not available'
        ], 503);


    }

    /**
     * Send phone validation sms.
     *
     * @param  SendVerificationCodeRequest  $request
     * @return JsonResponse
     */
    public function getActiveVerification(SendVerificationCodeRequest $request): JsonResponse
    {
        $pvs = new PhoneVerificationService();
        $pvs->test = false;

        $activeVerification = $pvs->phoneActiveVerification($request->input('data.phone'));

        if($activeVerification){
            return response()->json([
                'type' => 'old',
                'phone_verification' => new PhoneVerificationResource($activeVerification)
            ]);
        }

        return response()->json([
            'phone_verification' => null,
            'type' => 'error',
            'message' => 'service not available'
        ], 503);

    }

    /**
     * Send phone validation sms.
     *
     * @param  CheckVerificationCodeRequest  $request
     * @return JsonResponse
     */
    public function checkVerificationCode(CheckVerificationCodeRequest $request): JsonResponse
    {
        $pvs = new PhoneVerificationService();

        $result = $pvs->checkVerificationCode([
            'phone' => $request->input('data.phone'),
            'code' => $request->input('data.code'),
            'mode' => $request->input('data.mode'),
        ]);

        if($result){
            $response = [
                'status' => 'ok',
                'message' => 'verified',
            ];

            // send jwt token if login mode
            if($request->has('data.mode') && $request->input('data.mode') == 'login'){
                $user = User::whereHas('profile', function($q) use ($request){
                    $q->where('phone', Helper::clearPhone($request->input('data.phone')));
                })->first();

                if($user){
                    $r = auth()->login($user);
                    $token = $r;
                    $response['token'] = $token;
                }
            }

            return response()->json($response);
        }

        return response()->json([
            'type' => 'false',
            'message' => 'code not valid'
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
