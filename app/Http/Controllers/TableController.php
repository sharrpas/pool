<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TableController extends Controller
{
    public function index()
    {
        return $this->success(Table::all());
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

        Table::query()->create([
            'name' => $request->name,
            'pic' => $ImageName,
            'price' => $request->price ?? rand(null,1000)
        ]);

        return $this->success('اضافه شد');
    }
}
