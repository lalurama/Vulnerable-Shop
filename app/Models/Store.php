<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = ['name', 'location'];

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }
}
