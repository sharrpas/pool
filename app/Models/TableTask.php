<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableTask extends Model
{
    use HasFactory;
    protected $guarded =[];
    public $timestamps = false;

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
