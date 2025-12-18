<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'caption',
        'title_translations',
        'caption_translations',
        'image_path',
        'link_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'title_translations' => 'array',
        'caption_translations' => 'array',
    ];
}
