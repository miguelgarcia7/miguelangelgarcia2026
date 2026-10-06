export type Role = 'user' | 'assistant';

export interface Message {
    id: string;
    role: Role;
    content: string;
    /** Titles of knowledge entries the answer was grounded in. */
    sources?: string[];
    /** True while the answer is still streaming in. */
    pending?: boolean;
    /** Set when the request failed; content holds the visitor-safe text. */
    error?: boolean;
    /** True when the assistant said it had no documented information. */
    unanswered?: boolean;
}

export interface AskConfig {
    endpoint: string;
    csrfToken: string;
    suggestions: string[];
    maxLength: number;
    maxHistory: number;
    contactHref: string;
}

/** How a question was asked, reported to Google Analytics. */
export type AskSource = 'typed' | 'suggestion';

declare global {
    interface Window {
        /** Defined only when Google Analytics is enabled (GOOGLE_ANALYTICS_ID). */
        gtag?: (command: 'event', name: string, params?: Record<string, string>) => void;
    }
}
