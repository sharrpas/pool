<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Table extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function tasks()
    {
        return $this->hasMany(TableTask::class);
    }

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

//    protected function pic(): Attribute
//    {
//        return Attribute::make(
//            get: fn ($value) => Storage::url('images/'. $value),
//        );
//    }
}
