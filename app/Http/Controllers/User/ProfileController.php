<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return $this->success($user);
    }

    public function update(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'name' => 'string',
            'username' => 'unique:App\Models\User,username|min:4',
            'avatar' => 'mimes:jpeg,png,jpg|max:10240',

        ]);
        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());

        $user = auth()->user();

        if ($request->avatar) {
            Storage::delete('images/' . $user->avatar);
            $AvatarName = date('Ymdhis') . rand(100, 999) . '0.jpg';
            Storage::putFileAs('images', $request->file('avatar'), $AvatarName);
        }

        $user->update([
            'name' => $request->name ?? $user->name,
            'username' => $request->username ?? $user->username,
            'avatar' => $request->avatar ? $AvatarName : $user->avatar,
        ]);

        return $this->success('تغییرات اعمال شد');

    }

}
