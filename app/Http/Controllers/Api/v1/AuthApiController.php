<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthApiController extends Controller
{
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $user = auth()->user();// returns the logged user data

        // all good so return the token
        return response()->json(compact('token','user'));
    }

    public function getAuthenticatedUser()
{
	try {

		if (! $user = JWTAuth::parseToken()->authenticate()) {
			return response()->json(['user_not_found'], 404);
		}

	} catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

		return response()->json(['token_expired'], 401);
 
	} catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

		return response()->json(['token_invalid'], 401);

	} catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

		return response()->json(['token_absent'], 401);

	}

	// the token is valid and we have found the user via the sub claim
	return response()->json(compact('user'));
}

public function refreshToken(){
    
    if(!$token = JWTAuth::getToken()) 
        return response()->json(['error' => 'token not send'],401);
    try {
        $token = JWTAuth::refresh();
    } catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
        return response()->json(['token_invalid'], 401);
    }
    return response()->json(compact('token'));
}
}
