<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class GymController extends Controller
{

    public function index()
    {
        $gym = Gym::query()->with('manager')->get();
        return $this->success($gym);
    }
//    public function add_gym(Request $request)
//    {
//        $validated_data = Validator::make($request->all(), [
//            'gym_name' => 'required' ,
//            'manager_name' => 'required' ,
//            'mobile' => 'unique:App\Models\User,mobile|required|regex:/(09)[0-9]{9}/|size:11',
//            'username' => 'unique:App\Models\User,username|required',
//            'address' => 'string',
//            'city' => ['required', Rule::in(config('settings.cities'))],
//            'password' => [Password::required(), Password::min(4)->numbers()/*->mixedCase()->letters()->symbols()->uncompromised()*/, 'confirmed'],
//            ]);
//        if ($validated_data->fails())
//            return $this->error(Status::VALIDATION_FAILED,$validated_data->errors()->first());
//
//        DB::beginTransaction();
//        try {
//            $manager = User::query()->create([
//                'name' => $request->manager_name,
//                'username' => $request->username,
//                'mobile' => $request->mobile,
//                'password' => Hash::make($request->password),
//            ]);
//            $manager->roles()->attach(Role::query()->where('name','manager')->first()->id);
//
//            $manager->gym()->create([
//                'name' => $request->gym_name,
//                'city' => $request->city,
//                'address' => $request->address  ,
//            ]);
//
//            DB::commit();
//            return $this->success($request->gym_name.' اضافه شد ');
//
//        }catch (\Exception $e) {
//            DB::rollback();
//            return $this->error(Status::OPERATION_ERROR,$e->getMessage());
//        }
//
//
//
//
//    }
}
