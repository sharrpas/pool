<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gym extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public function manager()
    {
        return $this->belongsTo(User::class,'manager_id');
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
