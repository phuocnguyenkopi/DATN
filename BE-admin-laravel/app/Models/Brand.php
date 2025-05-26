<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    //
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'name',
        'logo',
        'slug',
        'status'
    ];

    // Quan hệ với Product
    public function products()
    {
        return $this->hasMany(Product::class, 'id_brand','id');
    }
}
