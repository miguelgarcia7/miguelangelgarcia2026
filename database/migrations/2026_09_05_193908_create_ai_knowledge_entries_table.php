<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_knowledge_entries', function (Blueprint $table) {
            $table->id();
            $table->string('category', 80)->index();
            // "general" entries carry their text in "content"; "star" entries
            // carry it in the four STAR fields and may leave "content" empty.
            $table->string('kind', 20)->default('general');
            $table->string('title', 160);
            $table->string('slug', 180)->unique();
            $table->string('summary', 500)->nullable();
            $table->text('content')->nullable();
            $table->text('situation')->nullable();
            $table->text('task')->nullable();
            $table->text('action')->nullable();
            $table->text('result')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedTinyInteger('importance')->default(3)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_knowledge_entries');
    }
};
