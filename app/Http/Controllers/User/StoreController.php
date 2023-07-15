<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\GymResource;
use App\Http\Resources\User\ProductCollection;
use App\Http\Resources\User\ProductResource;
use App\Models\City;
use App\Models\Gym;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {

        $products = Product::query()->get();
        return $this->success(ProductCollection::collection($products->load('gym')));
    }

    public function show(Product $product)
    {
        return $this->success(ProductResource::make($product->load('gym')));
    }
}
