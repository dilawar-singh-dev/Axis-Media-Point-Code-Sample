<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{
    public $successStatus = 200;
    /**
         * login api
         *
         * @return \Illuminate\Http\Response
         */
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $token =  $user->createToken('MyApp')->accessToken;
            $response = [
                'user' => $user,
                'token' => $token
            ];
            return JsonResponse(200,'success',$response);
        } else {
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }
    /**
         * Register api
         *
         * @return \Illuminate\Http\Response
         */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
        return response()->json(['success'=>$success], $this->successStatus);
    }
    /**
         * details api
         *
         * @return \Illuminate\Http\Response
         */
    public function user()
    {
        $user = Auth::user();
        return JsonResponse(200,'success',$user);
    }
}
