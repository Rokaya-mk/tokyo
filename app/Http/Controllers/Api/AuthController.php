<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
    $validator = Validator::make($request->all(), [
    'name' => 'required',
    'email' => 'required|email',
    'password' => 'required|confirmed ',
    'password_confirmation' =>' required',



    ]);
    if ($validator->fails()) {
    return response()->json(['error'=>$validator->errors()], 401);
    }
    $input = $request->all();
    $input['password'] =  bcrypt($input['password']);
    $user = User::create($input);
    $accessToken = $user->createToken('authToken')->accessToken;
     $email= $input['email'];
    return response()->json(['access_token'=> $accessToken]);
    }




    public function login(Request $request)
    {
         $loginData = $request->validate([
             'email' => 'email|required',
             'password' => 'required'
         ]);

         if(!Auth::attempt($loginData)) {
             return response(['message'=>'Invalid credentials']);
         }
         //$user=Auth::user();
         $user = User::where('email', $request->email)->first();
         $accessToken = $user->createToken('authToken')->accessToken;

         return response(['user' => auth()->user(), 'access_token' => $accessToken]);
        }

 public function logoutApi(Request $request)
{

    if (Auth::check()) {
        $request->user()->token()->revoke();
    return response()->json([
        'message' => 'Successfully logged out']);
    }
    return response()->json([
        'error' => 'Unable to logout user',
        'code' => 401,

    ], 401);

}
    // public function logout(){
    //     if( auth()->user()){
    //         $user = auth()->user();
    //         $user['access_token'] = null ;
    //         $user->save() ;
    //         return response()->json(['message' => 'Thank you for using our application']);
    //     }
    //     return response()->json([
    //         'error' => 'Unable to logout user',
    //         'code' => 401,

    //     ], 401);
    // }
}
