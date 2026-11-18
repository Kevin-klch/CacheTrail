<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Feedback that visitors send with the feedback button. Guests may send feedback too, so the user is optional.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category', 20);
            $table->text('message');
            $table->string('contact')->nullable();
            $table->string('page_url', 500)->nullable();
            $table->timestamp('done_at')->nullable();
            $table->timestamps();

            $table->index(['done_at', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
