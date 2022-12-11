<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Table extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function pic(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Storage::url('images/'. $value),
        );
    }
}
