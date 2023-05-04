<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\GymResource;
use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index($city)
    {
        $array = config('settings.cities');
        array_push($array, 'all');
        $validated_data = Validator::make(['city' => $city], [
            'city' => ['required', Rule::in($array)],
        ]);
        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());

//        $result = [];
//        foreach (config('settings.cities') as $c){
//            $result[$c] = [];
//        }
//        Gym::query()->orderBy('city')->get()->map(function ($gym) use (&$result){
//            array_push($result[$gym->city],GymResource::make($gym));
//        });

        $city == 'all' ? $gyms = Gym::query()->orderBy('city')->get() : $gyms = Gym::query()->where('city',$city)->get();
        return $this->success($gyms);
    }

    public function show(Gym $gym)
    {
        return $this->success(GymResource::make($gym->load('tables')));
    }
}
