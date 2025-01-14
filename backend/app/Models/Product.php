<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'expiration_date',
        'image',
        'category_id',
    ];


    // Define o relacionamento com a categoria
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Verifica se o produto está expirado
    public function isExpired(): bool
    {
        return $this->expiration_date < now();
    }
}
