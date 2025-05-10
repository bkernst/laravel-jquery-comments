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
                if (! Schema::hasColumn('comments', 'created_at')) {
                    $table->dateTime('created_at')->nullable();
                }
                if (! Schema::hasColumn('comments', 'updated_at')) {
                    $table->dateTime('updated_at')->nullable();
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
                if (Schema::hasColumn('comments', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('comments', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
};
