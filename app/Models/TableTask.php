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

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
