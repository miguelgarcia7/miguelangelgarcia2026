<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per visitor question. Deliberately holds no IP address, user
     * agent, or full conversation — only what is needed to see which
     * questions get asked, which entries answer them, and what they cost.
     */
    public function up(): void
    {
        Schema::create('ai_question_logs', function (Blueprint $table) {
            $table->id();
            $table->string('question', 1000);
            $table->string('question_type', 30)->nullable()->index();
            $table->json('classification')->nullable();
            $table->json('retrieved_entry_ids')->nullable();
            $table->boolean('answered')->default(false)->index();
            $table->text('answer')->nullable();
            $table->string('model', 80)->nullable();
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->unsignedInteger('cache_read_tokens')->default(0);
            $table->decimal('estimated_cost', 10, 6)->default(0);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->string('error', 500)->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_question_logs');
    }
};
