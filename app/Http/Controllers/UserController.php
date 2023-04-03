<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use function Symfony\Component\String\u;
use Cryptommer\Smsir\Smsir;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'username' => 'required',//or mobile number
            'password' => 'required'
        ]);
        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors());


        if (!$user = User::query()
            ->where('username', $request->username)
            ->orWhere('mobile',$request->username)
            ->first()) {
            return $this->error(Status::AUTHENTICATION_FAILED, 'نام کاربری یا رمز عبور اشتباه است');
        }

        $pass_check = Hash::check($request->password, $user->password);

        if ($user && $pass_check) {
            return $this->success([
                'user' => $user->name,
                'gym_name' => $user->gym()->first()->name ?? null,
                'username' => $user->username,
                'role' => $user->roles()->first()->name,
                'token' => $user->createToken('token_base_name')->plainTextToken
            ]);
        } else {
            return $this->error(Status::AUTHENTICATION_FAILED, 'نام کاربری یا رمز عبور اشتباه است');
        }

    }


    public function logout()
    {
        /** @var User $user */
        $user = auth()->user();

        $user->tokens()->delete();

        return $this->success();
    }

    public function requestCode(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'mobile' => 'required|regex:/(09)[0-9]{9}/|size:11',
        ]);
        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors());


        if (!$user = User::query()->where('mobile', $request->mobile)->first()) {
            return $this->error(Status::OPERATION_ERROR, 'شماره تماس ذخیره نشده است');
        }

        $verification_code = $user->verificationCode()->create([
            'code' => rand(1000, 9999),
        ]);

        $send = smsir::Send();
        $parameter = new \Cryptommer\Smsir\Objects\Parameters('CODE', $verification_code->code);
        $parameters = array($parameter);
        $send->Verify($user->mobile, '812390', $parameters);

        return $this->success([
            'کد بازیابی ارسال شد',
            'user' => $user,
        ]);
    }

    public function changePass(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'verification_code' => 'required|integer',
            'new_password' => [
                Password::required(),
                Password::min(8)->numbers()->mixedCase()->letters(),
                'confirmed'
            ],
        ]);
        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors());

        if (!$verification_code = VerificationCode::query()
            ->where('code', $request->verification_code)
            ->where('created_at', '>=', Carbon::now()->subMinute(4))
            ->Where('verified_at',null)
            ->first()) {
            return $this->error(Status::OPERATION_ERROR, 'کد بازیابی نادرست است');
        }
        $user = $verification_code->user()->first();

        $verification_code->update([
            'verified_at' => Carbon::now(),
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        $user->tokens()->delete();

        return $this->success([
            'رمز عبور با موفقیت تغییر یافت',
            'user' => $user,
        ]);
    }

}
