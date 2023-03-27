<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function manager()
    {
        return $this->belongsTo(User::class,'manager_id');
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }
}
