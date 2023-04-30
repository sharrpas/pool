<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\BuffetResource;
use App\Http\Resources\ProductResource;
use App\Models\Buffet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BuffetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $buffets = auth()->user()->gym()->first()->buffets()->get();
        return $this->success(BuffetResource::collection($buffets));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'title' => 'required|string',
            'image' => 'mimes:jpeg,png,jpg',
            'pic' => 'string',
            'price' => 'required|integer',
        ]);

        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());

        $gym = auth()->user()->gym()->first();

        DB::beginTransaction();
        try {

            if ($request->image) {
                $ImageName = date('Ymdhis') . rand(100, 999) . '.jpg';
                Storage::putFileAs('images/buffets/', $request->file('image'), $ImageName);
            }

            $product = $gym->buffets()->create([
                'title' => $request->title,
                'image' => $ImageName ?? null,
                'pic' => $request->pic ?? null,
                'price' => $request->price
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->error(Status::Unexpected_Problem, $exception->getMessage());
        }

        return $this->success($product->title . ' به بوفه اضافه شد ');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Buffet $buffet)
    {
        if ($buffet->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        return $this->success(BuffetResource::make($buffet));

    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Buffet $buffet)
    {
        if ($buffet->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        $validated_data = Validator::make($request->all(), [
            'title' => 'string',
            'image' => 'mimes:jpeg,png,jpg',
            'pic' => 'string',
            'price' => 'integer',
        ]);

        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());

        if ($request->image) {
            Storage::delete('images/buffets/' . $request->image);
            $ImageName = date('Ymdhis') . rand(100, 999) . '.jpg';
            Storage::putFileAs('images/buffets/', $request->file('image'), $ImageName);
        }


        $buffet->update([
            'title' => $request->title ?? $buffet->title,
            'image' => $ImageName ?? $buffet->image,
            'pic' => $request->pic ?? null,
            'price' => $request->price ?? $buffet->price,
        ]);

        return $this->success($buffet->title . ' ویرایش شد ');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Buffet $buffet)
    {
        if ($buffet->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        Storage::delete('images/buffets/' . $buffet->image);

        $buffet->delete();

        return $this->success('محصول حذف شد');

    }
}
