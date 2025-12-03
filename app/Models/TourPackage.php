<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'short_description',
        'description',
        'price',
        'duration',
        'location',
        'is_active',
        'is_featured',
        'primary_image_path',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function images()
    {
        return $this->hasMany(TourImage::class);
    }
}
