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
            if (!Schema::hasColumn('events','dress_code')) {
                $table->string('dress_code')->nullable()->after('venue');
            }
            if (!Schema::hasColumn('events','theme')) {
                $table->json('theme')->nullable()->after('dress_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('dress_code');
            $table->dropColumn('theme');
        });
    }
};
