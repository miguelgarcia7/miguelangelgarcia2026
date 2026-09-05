<?php

namespace App\Models;

use Database\Factories\AiQuestionLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * One visitor question and what happened to it. Kept for analytics and
 * for spotting questions the knowledge base cannot yet answer. Stores no
 * personal data about the visitor.
 */
class AiQuestionLog extends Model
{
    /** @use HasFactory<AiQuestionLogFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'question',
        'question_type',
        'classification',
        'retrieved_entry_ids',
        'answered',
        'answer',
        'model',
        'input_tokens',
        'output_tokens',
        'cache_read_tokens',
        'estimated_cost',
        'duration_ms',
        'error',
    ];

    protected function casts(): array
    {
        return [
            'classification' => 'array',
            'retrieved_entry_ids' => 'array',
            'answered' => 'boolean',
            'estimated_cost' => 'decimal:6',
            'created_at' => 'datetime',
        ];
    }
}
