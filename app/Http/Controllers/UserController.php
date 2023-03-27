<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);


        if (!$user = User::query()->where('username', $request->username)->first()) {
            return $this->error(Status::AUTHENTICATION_FAILED,'نام کاربری یا رمز عبور اشتباه است');
        }

        $pass_check = Hash::check($request->password, User::query()->where('username', $request->username)->firstOrFail()->password);

        if ($user && $pass_check) {
            return $this->success([
                'user' => $user->name,
                'role' => $user->roles()->first()->name,
                'token' => $user->createToken('token_base_name')->plainTextToken
            ]);
        } else {
            return $this->error(Status::AUTHENTICATION_FAILED,'نام کاربری یا رمز عبور اشتباه است');
        }

    }


    public function logout()
    {
        /** @var User $user */
        $user = auth()->user();

        $user->tokens()->delete();

        return $this->success();
    }


}
