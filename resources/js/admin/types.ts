export interface KnowledgeEntry {
    id: number;
    title: string;
    slug: string;
    categories: string[];
    kind: 'general' | 'star';
    summary: string;
    content: string;
    situation: string;
    task: string;
    action: string;
    result: string;
    tags: string[];
    importance: number;
    is_active: boolean;
    updated_at: string | null;
}

export type KnowledgeRow = Pick<
    KnowledgeEntry,
    'id' | 'title' | 'slug' | 'categories' | 'kind' | 'summary' | 'tags' | 'importance' | 'is_active' | 'updated_at'
>;

export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

export interface QuestionLog {
    id: number;
    question: string;
    question_type: string | null;
    answered: boolean;
    answer: string | null;
    sources: string[];
    model: string | null;
    input_tokens: number;
    output_tokens: number;
    estimated_cost: number;
    duration_ms: number;
    error: string | null;
    created_at: string | null;
}

export interface SharedProps {
    auth: { user: { name: string; email: string } | null };
    flash: { success: string | null; error: string | null };
    [key: string]: unknown;
}

export interface PreviewResult {
    prompt_text: string;
    is_active: boolean;
    question: string;
    classification: {
        type: string;
        categories: string[];
        search_terms: string[];
        standalone_question: string | null;
        from_fallback: boolean;
    } | null;
    classification_source: 'model' | 'keywords' | null;
    ranking: {
        id: number;
        title: string;
        score: number;
        breakdown: Record<string, number>;
        selected: boolean;
        is_this: boolean;
    }[];
    would_be_used: boolean | null;
}
