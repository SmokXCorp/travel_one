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
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }
}
