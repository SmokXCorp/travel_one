<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_email',
        'contact_phone',
        'contact_address',
        'quick_links_translations',
        'social_links',
        'is_active',
        'updated_by_admin_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'quick_links_translations' => 'array',
        'social_links' => 'array',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }
}
