<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
    ];


    // Relacionamento com produtos
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
