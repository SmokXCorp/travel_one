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
        'title_translations',
        'subtitle_translations',
        'short_description_translations',
        'description_translations',
        'location_translations',
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
        'title_translations' => 'array',
        'subtitle_translations' => 'array',
        'short_description_translations' => 'array',
        'description_translations' => 'array',
        'location_translations' => 'array',
    ];

    public function images()
    {
        return $this->hasMany(TourImage::class);
    }
}
