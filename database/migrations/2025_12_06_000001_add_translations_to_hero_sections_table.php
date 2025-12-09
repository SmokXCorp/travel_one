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
        Schema::table('hero_sections', function (Blueprint $table) {
            $table->json('headline_translations')->nullable()->after('headline');
            $table->json('subheadline_translations')->nullable()->after('subheadline');
            $table->json('description_translations')->nullable()->after('description');
            $table->json('button_text_translations')->nullable()->after('button_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_sections', function (Blueprint $table) {
            $table->dropColumn([
                'headline_translations',
                'subheadline_translations',
                'description_translations',
                'button_text_translations',
            ]);
        });
    }
};
