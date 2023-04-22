<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function cities()
    {
        $cities = City::query()->select('city')->get()->map(function ($city){
            return ['value' => $city->city , 'label' => $city->city];
        });
        return $this->success($cities);
    }
}
