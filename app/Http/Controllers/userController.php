<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users',
                'password' => 'required|string|min:6'
            ]);

            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->save();
            return response()->json(['message' => 'User has been registered..!', 'status' => 200]);
        } catch (Exception $exception) {

            return  response()->json(['message' => ($exception->getMessage())]);
        }
    }



    public function login(Request $request)
    {
        $login = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($login)) {
            return response()->json(['message' => 'Unauthorized User..!', 'status' => 401]);
        }


        $accessToken = $request->user()->createToken('Auth Token')->accessToken;


        return response()->json([
            'user' => Auth::user()->name,
            'status' => 200,
            'access_token' => $accessToken,
        ]);
    }

    public function logout(Request $request)
    {

        $token = $request->user()->token();
        $token->revoke();

        return response()->json(['message' => 'User has been Logout..!'], 200);
    }

    
    public function index()
    {
        return User::all();
    }
}
