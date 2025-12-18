<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_us_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('paragraph_one')->nullable();
            $table->text('paragraph_two')->nullable();
            $table->text('paragraph_three')->nullable();
            $table->json('title_translations')->nullable();
            $table->json('paragraph_one_translations')->nullable();
            $table->json('paragraph_two_translations')->nullable();
            $table->json('paragraph_three_translations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_us_sections');
    }
};
