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
        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                if (! Schema::hasColumn('comments', 'deleted')) {
                    $table->boolean('deleted')->default(false);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('comments')) {
            Schema::table('course_collections', function (Blueprint $table) {
                if (Schema::hasColumn('comments', 'deleted')) {
                    $table->dropColumn('deleted');
                }
            });
        }
    }
};
