<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TableTask extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded =[];
    public $timestamps = false;
    protected $hidden = [
        'deleted_at',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function player()
    {
        return $this->belongsTo(User::class,'player_id');
    }
}
