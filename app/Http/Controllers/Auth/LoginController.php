<?php

namespace App\Http\Controllers\Auth;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

     
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => 'Incorrect username or password. Please try again.',
            ]);
        }
    }
    
    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return response()->json([
            'error' => 'Incorrect username or password. Please try again.',
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        try{

            $http = new \GuzzleHttp\Client;
            
            $response = $http->post(config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $request->input('email'),
                    'password' => $request->input('password'),
                ],
    
                'headers' => [
                    'Accept'  => 'application/json'
                ],
            ]);
            
            return json_decode((string) $response->getBody(), true);
    
        }catch(\GuzzleHttp\Exception\BadResponseException $e){
            $this->guard()->logout();
            
            $request->session()->invalidate();
            
            return response()->json([
                'error' => 'There was an error retrieving authentication. Please refresh and try again.'
            ]); 
        }
    }
}
