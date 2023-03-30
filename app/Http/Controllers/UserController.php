<?php

namespace App\Http\Controllers;

use App\Http\Requests\auth\LoginRequest;
use App\Http\Requests\auth\SignupRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function actionLogin(LoginRequest $request){
        $userData = $request->only('mobile', 'password');
        if(! Auth::attempt($userData)){
            return $this->customErrorResponse([], 'invalid username or password!', 401);
        }

        $user = User::findByMobile($userData['mobile']);
        return $this->successResponse(
            [
                'token' => $user->createToken(User::TOKEN_NAME)->plainTextToken,
            ],
            'You logged in successfully',
            200
        );
    }

    public function actionSignup(SignupRequest $request){
        $userData = $request->all();
        $userData['password'] = bcrypt($userData['password']);

        $user = User::create($userData);
        return $this->successResponse(
            [
                'token' => $user->createToken(User::TOKEN_NAME)->plainTextToken,
            ],
            'Your Signup is successfully',
            200
        );
    }
}
