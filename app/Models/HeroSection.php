<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'headline',
        'subheadline',
        'description',
        'button_text',
        'button_url',
        'image_path',
        'is_active',
        'updated_by_admin_id',
        'headline_translations',
        'subheadline_translations',
        'description_translations',
        'button_text_translations',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'headline_translations' => 'array',
        'subheadline_translations' => 'array',
        'description_translations' => 'array',
        'button_text_translations' => 'array',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }
}
