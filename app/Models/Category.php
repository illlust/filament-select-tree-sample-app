<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $connection = 'mysql';
    protected $table = 'categories';

    public function product()
    {
        return $this->hasOne(Product::class);
    }

    protected $fillable = [
        'id',
        'name',
        'category_id',
    ];
}
