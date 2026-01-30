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
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'hero_image_path')) {
                $table->string('hero_image_path')->nullable()->after('theme');
            }
            if (!Schema::hasColumn('events', 'music_path')) {
                $table->string('music_path')->nullable()->after('hero_image_path');
            }
            if (!Schema::hasColumn('events', 'music_title')) {
                $table->string('music_title')->nullable()->after('music_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('hero_image_path');
            $table->dropColumn('music_path');
            $table->dropColumn('music_title');
        });
    }
};
