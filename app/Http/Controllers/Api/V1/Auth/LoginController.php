<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\loginrequest;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(loginrequest $request)
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
                'access_token'=>$user->CreateToken($device)->plainTextToken
            ]
            );
    }
}
