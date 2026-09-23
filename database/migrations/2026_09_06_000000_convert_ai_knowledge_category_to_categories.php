<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * An entry can belong to several categories (a STAR story is usually
 * Leadership and Problem Solving at once), so the single "category"
 * column becomes a "categories" JSON array. Existing rows are converted,
 * not dropped.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_knowledge_entries', function (Blueprint $table) {
            $table->json('categories')->nullable()->after('kind');
        });

        DB::table('ai_knowledge_entries')->select(['id', 'category'])->orderBy('id')->each(function ($row) {
            DB::table('ai_knowledge_entries')
                ->where('id', $row->id)
                ->update(['categories' => json_encode(array_values(array_filter([$row->category])))]);
        });

        Schema::table('ai_knowledge_entries', function (Blueprint $table) {
            $table->dropIndex(['category']);
        });

        Schema::table('ai_knowledge_entries', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('ai_knowledge_entries', function (Blueprint $table) {
            $table->string('category', 80)->nullable()->index()->after('kind');
        });

        DB::table('ai_knowledge_entries')->select(['id', 'categories'])->orderBy('id')->each(function ($row) {
            $categories = json_decode((string) $row->categories, true) ?: [];

            DB::table('ai_knowledge_entries')
                ->where('id', $row->id)
                ->update(['category' => $categories[0] ?? 'Uncategorised']);
        });

        Schema::table('ai_knowledge_entries', function (Blueprint $table) {
            $table->dropColumn('categories');
        });
    }
};
