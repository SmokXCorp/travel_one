<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\HeroSection;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::query()->firstOrCreate(
            ['email' => config('app.admin_email')],
            [
                'name' => 'Primary Admin',
                'password' => config('app.admin_password'),
            ]
        );

        if (!HeroSection::query()->exists()) {
            HeroSection::query()->create([
                'headline' => 'Discover the world with us',
                'subheadline' => 'Tailored adventures & curated escapes',
                'description' => 'Use the admin area to update this hero copy and photo.',
                'button_text' => 'Explore Tours',
                'button_url' => '/',
                'image_path' => null,
                'updated_by_admin_id' => $admin->id,
            ]);
        }
    }
}
