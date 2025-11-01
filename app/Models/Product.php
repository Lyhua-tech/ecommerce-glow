<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_url',
        'description',
        'sku',        
        'color_code',
        'price',
        'sale_price',
        'sale_start_date',
        'sale_end_date',  
        'subcategory_id',
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function subcategory(): BelongsTo{
        return $this->belongsTo(Subcategory::class);
    }

    public function images()
    {
        // 2. Add this function
        return $this->hasMany(ProductImage::class);
    }
}
