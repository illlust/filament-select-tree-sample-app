<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'mysql';
    protected $table = 'products';

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected $fillable = [
        'id',
        'name',
        'category_id',
    ];
}
