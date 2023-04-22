<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Role;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());


        if (!$user = User::query()
            ->where('username', $request->username)
            ->orWhere('mobile', $request->username)
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

    public function signup($role, Request $request)
    {
        if ($role == "user") {
            $validated_data = Validator::make($request->all(), [
                'name' => 'required',
                'username' => 'unique:App\Models\User,username|required|min:4',
                'mobile' => 'unique:App\Models\User,mobile|required|regex:/(09)[0-9]{9}/|size:11',
                'password' => [Password::required(), Password::min(4)->numbers(), 'confirmed'],
            ]);
            if ($validated_data->fails())
                return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());

            $user = User::query()->create([
                'name' => $request->name,
                'username' => $request->username,
                'mobile' => $request->mobile,
                'password' => Hash::make($request->password),
            ]);
            $user->roles()->attach(Role::query()->where('name', 'user')->first()->id);


        } elseif ($role == "manager") {
            $validated_data = Validator::make($request->all(), [
                'gym_name' => 'required',
                'manager_name' => 'required',
                'mobile' => 'unique:App\Models\User,mobile|required|regex:/(09)[0-9]{9}/|size:11',
                'username' => 'unique:App\Models\User,username|required|min:4',
                'address' => 'required|string|min:15',
                'city' => ['required', Rule::in(config('settings.cities'))],
                'password' => [Password::required(), Password::min(4)->numbers()/*->mixedCase()->letters()->symbols()->uncompromised()*/, 'confirmed'],
            ]);
            if ($validated_data->fails())
                return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());

            DB::beginTransaction();
            try {
                $user = User::query()->create([
                    'name' => $request->manager_name,
                    'username' => $request->username,
                    'mobile' => $request->mobile,
                    'password' => Hash::make($request->password),
                ]);
                $user->roles()->attach(Role::query()->where('name', 'manager')->first()->id);

                $user->gym()->create([
                    'name' => $request->gym_name,
                    'city' => $request->city,
                    'address' => $request->address,
                ]);

                DB::commit();

            } catch (\Exception $e) {
                DB::rollback();
                return $this->error(Status::OPERATION_ERROR, $e->getMessage());
            }

        }
        else{
            return $this->error(Status::NOT_FOUND);
        }
        return $this->success([
            'user' => $user->name,
            'gym_name' => $user->gym()->first()->name ?? null,
            'username' => $user->username,
            'role' => $user->roles()->first()->name,
            'token' => $user->createToken('token_base_name')->plainTextToken
        ]);
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
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());


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
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());

        if (!$verification_code = VerificationCode::query()
            ->where('code', $request->verification_code)
            ->where('created_at', '>=', Carbon::now()->subMinute(4))
            ->Where('verified_at', null)
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
