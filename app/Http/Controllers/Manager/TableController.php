<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\TableResource;
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
        return $this->success(TableResource::collection($gym->tables()->get()));
    }

    public function show(Table $table)
    {
        if ($table->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        return $this->success($table);
    }

    public function store(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'name' => 'required|string',
//            'pic' => 'required|mimes:jpeg,png,jpg',
            'pic' => 'required',
            'price' => 'required|integer'
        ]);

        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());


//        $ImageName = date('Ymdhis') . rand(100, 999) . '.jpg';
//        Storage::putFileAs('images', $request->file('pic'), $ImageName);

        $user = auth()->user();
        $gym = $user->gym()->first();
        $gym->tables()->create([
            'name' => $request->name,
//            'pic' => $ImageName,
            'pic' => $request->pic,
            'price' => $request->price ?? rand(null, 1000)
        ]);


        return $this->success('اضافه شد');
    }

    public function update(Table $table, Request $request)
    {
        if ($table->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        $validated_data = Validator::make($request->all(), [
            'name' => 'string',
//            'pic' => 'required',
            'price' => 'integer'
        ]);

        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());


        $table->update([
            'name' => $request->name??$table->name,
            'pic' => $request->pic ?? $table->pic,
            'price' => $request->price ?? $table->price,
        ]);

        return $this->success($table->name.' ویرایش شد ');

    }

    public function delete(Table $table)
    {
        if ($table->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        $table->tasks()->delete();
        $table->delete();

        return $this->success();
    }
}
