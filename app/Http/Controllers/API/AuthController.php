<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendVerificationCodeRequest;
use App\Http\Requests\Auth\CheckVerificationCodeRequest;

use App\Models\User;
use App\Services\PhoneVerificationService;

use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if ( !$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(RegisterRequest $request) {
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(new UserResource(auth()->user()));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Send phone validation sms.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendVerificationCode(SendVerificationCodeRequest $request)
    {
        $pvs = new PhoneVerificationService();
        $pvs->test = true;

        return response()->json([
            'phone_verification' => $pvs->createVerificationAndSendCode($request->input('data.phone'))
        ]);
    }

    /**
     * Send phone validation sms.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkVerificationCode(CheckVerificationCodeRequest $request)
    {
        $pvs = new PhoneVerificationService();

        $result = $pvs->checkVerificationCode([
            'phone' => $request->input('data.phone'),
            'code' => $request->input('data.code'),
        ]);

        if($result){
            $response = [
                'status' => 'ok',
                'message' => 'verified'
            ];

            // send jwt token if login mode
            if($request->has('data.type') && $request->input('data.type') === 'login'){
                $user = User::wherePhone($request->input('data.phone'))->first();
                $token = $this->respondWithToken(auth()->login($user));
                $response['token'] = $token;
            }

            return response()->json($response);
        }

        return response()->json([
            'type' => 'false',
            'message' => 'code not valid'
        ], 401);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
