<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\GymResource;
use App\Models\City;
use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        //todo return manager info
        $gym = auth()->user()->gym()->first();
        return $this->success(GymResource::make($gym));
    }

    public function update(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'name' => 'string',
            'about' => 'string|min:10',
            'address' => 'string|min:15',
            'avatar' => 'mimes:jpeg,png,jpg',
            'image' => 'mimes:jpeg,png,jpg',
            'lat' => 'string',
            'long' => 'string',
            'city' => 'string',
        ]);
        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());

        $gym = auth()->user()->gym()->first();

        $request->city ? $city = City::query()->where('city',$request->city)->first()->id : $city = $gym->city_id;


        if ($request->image) {
            Storage::delete('images/' . $gym->image);
            $ImageName = date('Ymdhis') . rand(100, 999) . '.jpg';
            Storage::putFileAs('images', $request->file('image'), $ImageName);
        }
        if ($request->avatar) {
            Storage::delete('images/' . $gym->avatar);
            $AvatarName = date('Ymdhis') . rand(100, 999) . '0.jpg';
            Storage::putFileAs('images', $request->file('avatar'), $AvatarName);
        }

        $gym->update([
            'name' => $request->name ?? $gym->name,
            'about' => $request->about ?? $gym->about,
            'address' => $request->address ?? $gym->address,
            'avatar' => $request->avatar ? $AvatarName : $gym->avatar,
            'image' => $request->image ? $ImageName : $gym->image,
            'lat' => $request->lat ?? $gym->lat,
            'long' => $request->long ?? $gym->long,
            'city_id' => $city,
        ]);

        return $this->success('تغییرات اعمال شد');

    }
}
