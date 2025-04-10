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
        if (! Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->foreign('parent_id')
                    ->references('id')
                    ->on('comments')
                    ->onDelete('set null')
                    ->constrained()
                    ->cascadeOnDelete();
                $table->unsignedTinyInteger('approved')->default(0);
                $table->string('name', 150);
                $table->string('email', 150);
                $table->longText('comment');
                $table->timestamps();

                // Add indexes
                $table->index('parent_id');
                $table->index('approved');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
