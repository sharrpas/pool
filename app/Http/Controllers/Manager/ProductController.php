<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = auth()->user()->gym()->first()->products()->get();
        return $this->success(ProductResource::collection($products));
    }

    public function show(Product $product)
    {
        if ($product->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        return $this->success(ProductResource::make($product));
    }

    public function store(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'title' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'mimes:jpeg,png,jpg',
            'description' => 'required|string',
            'price' => 'required|integer',
        ]);

        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());

        $gym = auth()->user()->gym()->first();

        DB::beginTransaction();
        try {

            $imagesPath = [];
            foreach ($request->file('images') as $image) {
                $imagesName = date('Ymdhis') . rand(100, 999) . '.jpg';
                array_push($imagesPath,$imagesName);
                Storage::putFileAs('images/stores', $image, $imagesName);
            }

            $gym->products()->create([
                'title' => $request->title,
                'images' => implode(',',$imagesPath),
                'description' => $request->description,
                'price' => $request->price
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->error(Status::Unexpected_Problem,$exception->getMessage());
        }

        return $this->success($imagesPath);
    }

    public function update()
    {

    }

    public function delete()
    {

    }
}
