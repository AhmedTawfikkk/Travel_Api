<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Hash;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\loginrequest;
use App\Http\Controllers\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Notifications\EmailVerificationNotification;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(loginrequest $request,EmailVerificationController $otpcontroller)
    {
        $user=User::where('email',$request->email)->first();
        if(!$user|| !Hash::check($request->password,$user->password))
        {
            return response()->json(
                [
                    'error'=>'the provided credentials are incorrect'
                ] ,422);
        }
        
        $device=substr($request->userAgent()??'',0,255);
        return response()->json(
            [
                'access_token'=>$user->createToken($device)->plainTextToken
            ]
            
            );
           
           
    }
}
