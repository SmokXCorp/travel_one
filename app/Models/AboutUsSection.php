<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUsSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'paragraph_one',
        'paragraph_two',
        'paragraph_three',
        'title_translations',
        'paragraph_one_translations',
        'paragraph_two_translations',
        'paragraph_three_translations',
        'is_active',
        'updated_by_admin_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'title_translations' => 'array',
        'paragraph_one_translations' => 'array',
        'paragraph_two_translations' => 'array',
        'paragraph_three_translations' => 'array',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }
}
