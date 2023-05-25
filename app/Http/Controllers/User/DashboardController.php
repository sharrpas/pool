<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\GymResource;
use App\Models\City;
use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index($city)
    {

        $city = City::query()->where('city', $city)->first();

        if ($city)
            $gyms = GymResource::collection($city->gyms()->inRandomOrder()->get());
        else {
            $gyms = Gym::query()->groupBy('city_id')->inRandomOrder()->get()
                ->map(function ($gym) {
                    return $gym->city()->first();
                })
                ->map(function ($city) {
                    return [
                        'city' => $city->city,
                        'gyms' => GymResource::collection($city->gyms()->inRandomOrder()->get())
                    ];
                });
        }
        return $this->success($gyms);
    }

    public function show(Gym $gym)
    {
        return $this->success(GymResource::make($gym->load('tables')));
    }
}
