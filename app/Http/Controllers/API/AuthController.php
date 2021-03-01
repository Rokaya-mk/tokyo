<?php


namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
    $validator = Validator::make($request->all(), [
    'name' => 'required',
    'email' => 'required|email',
    'password' => 'required',
    'c_password' =>' required|same:password',
    ]);
    if ($validator->fails()) {
    return response()->json(['error'=>$validator->errors()], 401);
    }
    $input = $request->all();
    $input['password'] = Hash::make($input['password']);
    $user = User::create($input);
    $accessToken = $user->createToken('authToken')->accessToken;

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
         $user=Auth::user();
         $accessToken = $user->createToken('authToken')->accessToken;

         return response(['user' => auth()->user(), 'access_token' => $accessToken]);
        }

 public function logoutApi(Request $request)
{

    if (Auth::check()) {
        $request->user()->token()->revoke();
    return response()->json([
        'message' => 'Successfully logged out'
    ]);
    }


    }
}
