<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    public function index()
    {
        $gym = auth()->user()->gym()->first();
        return $this->success($gym->tables()->get());
    }

    public function store(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'name' => 'required|string',
            'pic' => 'required|mimes:jpeg,png,jpg',
            'price' => 'required|integer'
        ]);

        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors());


        $ImageName = date('Ymdhis') . rand(100, 999) . '.jpg';
        Storage::putFileAs('images', $request->file('pic'), $ImageName);

        $user = auth()->user();
        $gym = $user->gym()->first();
        $gym->tables()->create([
            'name' => $request->name,
            'pic' => $ImageName,
            'price' => $request->price ?? rand(null,1000)
        ]);


        return $this->success('اضافه شد');
    }
}