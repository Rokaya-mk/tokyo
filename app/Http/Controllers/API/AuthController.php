<?php



namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator ;


class AuthController extends BaseController
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required',
            'c_password'=>'required|same:password'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validate Error',$validator->errors() );
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('@123*EMOOO*457##')->accessToken;
        $success['name'] = $user->name;
        return $this->sendResponse($success, 'User registered Successfully!' );
    }



    public function login(Request $request)
    {


        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            //$user = Auth::user();
            $user = User::where('email', $request->email)->first();
            $success['token'] = $user->createToken('@123*EMOOO*457##')->accessToken;
            $success['name'] = $user->name;
            return $this->sendResponse($success, 'User Login Successfully!' );
        }

       else{
            return $this->sendError('Unauthorised',['error','Unauthorised'] );
        }

    }
}
