<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description_primary',
        'description_secondary',
        'title_translations',
        'description_primary_translations',
        'description_secondary_translations',
        'image_one_path',
        'image_two_path',
        'image_three_path',
        'is_active',
        'updated_by_admin_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'title_translations' => 'array',
        'description_primary_translations' => 'array',
        'description_secondary_translations' => 'array',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }
}
