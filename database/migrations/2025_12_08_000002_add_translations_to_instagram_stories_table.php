<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instagram_stories', function (Blueprint $table) {
            $table->json('title_translations')->nullable()->after('title');
            $table->json('caption_translations')->nullable()->after('caption');
        });
    }

    public function down(): void
    {
        Schema::table('instagram_stories', function (Blueprint $table) {
            $table->dropColumn(['title_translations', 'caption_translations']);
        });
    }
};
