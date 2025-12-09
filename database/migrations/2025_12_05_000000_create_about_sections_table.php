<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('about_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description_primary')->nullable();
            $table->text('description_secondary')->nullable();
            $table->json('title_translations')->nullable();
            $table->json('description_primary_translations')->nullable();
            $table->json('description_secondary_translations')->nullable();
            $table->string('image_one_path')->nullable();
            $table->string('image_two_path')->nullable();
            $table->string('image_three_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_sections');
    }
};
