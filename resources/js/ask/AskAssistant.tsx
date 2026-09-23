import { useCallback, useEffect, useId, useRef, useState, type FormEvent, type KeyboardEvent } from 'react';
import { Markdown } from './Markdown';
import { askStream, AskRequestError } from './stream';
import type { AskConfig, Message } from './types';

const NO_INFORMATION_MARKER = "I don't have documented information";

let counter = 0;
const nextId = () => `m${Date.now().toString(36)}${(counter++).toString(36)}`;

export function AskAssistant({ config }: { config: AskConfig }) {
    const [messages, setMessages] = useState<Message[]>([]);
    const [draft, setDraft] = useState('');
    const [busy, setBusy] = useState(false);
    const [formError, setFormError] = useState<string | null>(null);

    const logRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLTextAreaElement>(null);
    const abortRef = useRef<AbortController | null>(null);
    const inputId = useId();
    const hintId = useId();

    // Keep the newest message in view as it streams in.
    useEffect(() => {
        const log = logRef.current;
        if (!log) return;
        log.scrollTo({ top: log.scrollHeight, behavior: 'smooth' });
    }, [messages]);

    useEffect(() => () => abortRef.current?.abort(), []);

    const ask = useCallback(
        async (rawQuestion: string) => {
            const question = rawQuestion.trim();
            if (!question || busy) return;

            if (question.length > config.maxLength) {
                setFormError(`Please keep your question under ${config.maxLength} characters.`);
                return;
            }

            setFormError(null);
            setDraft('');
            setBusy(true);

            const history = messages
                .filter((m) => !m.error && !m.pending && m.content.trim() !== '')
                .slice(-config.maxHistory)
                .map((m) => ({ role: m.role, content: m.content }));

            const userMessage: Message = { id: nextId(), role: 'user', content: question };
            const answerId = nextId();

            setMessages((prev) => [...prev, userMessage, { id: answerId, role: 'assistant', content: '', pending: true }]);

            const patchAnswer = (patch: Partial<Message> | ((m: Message) => Partial<Message>)) =>
                setMessages((prev) =>
                    prev.map((m) => (m.id === answerId ? { ...m, ...(typeof patch === 'function' ? patch(m) : patch) } : m)),
                );

            const controller = new AbortController();
            abortRef.current = controller;

            try {
                await askStream(
                    config.endpoint,
                    config.csrfToken,
                    { question, history },
                    {
                        onMeta: ({ sources }) => patchAnswer({ sources }),
                        onDelta: (text) => patchAnswer((m) => ({ content: m.content + text })),
                        onDone: ({ answered }) => patchAnswer({ pending: false, unanswered: !answered }),
                        onError: (message) => patchAnswer({ pending: false, error: true, content: message }),
                    },
                    controller.signal,
                );

                // The stream closed without a "done" frame — keep what arrived.
                patchAnswer((m) =>
                    m.pending
                        ? {
                              pending: false,
                              error: m.content === '',
                              content: m.content || 'The answer was cut off. Please try again.',
                          }
                        : {},
                );
            } catch (error) {
                if (controller.signal.aborted) return;

                const message =
                    error instanceof AskRequestError ? error.message : 'The assistant could not be reached. Please try again in a moment.';

                patchAnswer({ pending: false, error: true, content: message });
            } finally {
                setBusy(false);
                inputRef.current?.focus();
            }
        },
        [busy, config, messages],
    );

    const onSubmit = (event: FormEvent) => {
        event.preventDefault();
        void ask(draft);
    };

    const onKeyDown = (event: KeyboardEvent<HTMLTextAreaElement>) => {
        // Enter sends; Shift+Enter starts a new line.
        if (event.key === 'Enter' && !event.shiftKey && !event.nativeEvent.isComposing) {
            event.preventDefault();
            void ask(draft);
        }
    };

    const retry = (failedAnswerId: string) => {
        const index = messages.findIndex((m) => m.id === failedAnswerId);
        const question = index > 0 ? messages[index - 1] : undefined;
        if (!question || question.role !== 'user') return;

        setMessages((prev) => prev.filter((m) => m.id !== failedAnswerId && m.id !== question.id));
        // Defer so the state above is applied before the history is built.
        window.setTimeout(() => void ask(question.content), 0);
    };

    const reset = () => {
        abortRef.current?.abort();
        setMessages([]);
        setFormError(null);
        setBusy(false);
        inputRef.current?.focus();
    };

    const remaining = config.maxLength - draft.length;
    const isEmpty = messages.length === 0;

    return (
        <div className="overflow-hidden rounded-[24px] border border-line bg-surface-deep shadow-[0_30px_80px_-40px_rgba(0,0,0,0.8)]">
            {/* Transcript */}
            <div
                ref={logRef}
                role="log"
                aria-live="polite"
                aria-relevant="additions text"
                aria-label="Conversation with the assistant"
                className={`flex flex-col gap-5 overflow-y-auto px-5 py-6 min-[601px]:px-8 min-[601px]:py-8 ${
                    isEmpty ? 'min-h-[220px]' : 'max-h-[min(62vh,640px)] min-h-[320px]'
                }`}
            >
                {isEmpty ? (
                    <EmptyState suggestions={config.suggestions} onPick={(q) => void ask(q)} />
                ) : (
                    messages.map((message) => (
                        <MessageBubble key={message.id} message={message} contactHref={config.contactHref} onRetry={() => retry(message.id)} />
                    ))
                )}
            </div>

            {/* Follow-up suggestions once a conversation has started */}
            {!isEmpty && !busy && (
                <div className="flex flex-wrap items-center gap-2 border-t border-line px-5 py-3 min-[601px]:px-8">
                    {config.suggestions
                        .filter((q) => !messages.some((m) => m.role === 'user' && m.content === q))
                        .slice(0, 3)
                        .map((q) => (
                            <SuggestionChip key={q} label={q} small onClick={() => void ask(q)} />
                        ))}
                    <button
                        type="button"
                        onClick={reset}
                        className="ml-auto cursor-pointer py-1 text-[13px] font-medium text-faint underline-offset-4 hover:text-soft hover:underline"
                    >
                        Start over
                    </button>
                </div>
            )}

            {/* Composer */}
            <form onSubmit={onSubmit} className="border-t border-line bg-surface px-4 py-4 min-[601px]:px-6">
                <label htmlFor={inputId} className="sr-only">
                    Your question
                </label>
                {/* One focus indicator for the whole composer: the wrapper's
                    border turns accent on focus-within, and the textarea's own
                    focus ring is turned off in app.css so the two do not nest. */}
                <div
                    className={`flex items-end gap-3 rounded-[16px] border-2 bg-bg px-4 py-3 transition-colors focus-within:border-accent ${
                        formError ? 'border-danger' : 'border-line-control'
                    }`}
                >
                    <textarea
                        ref={inputRef}
                        id={inputId}
                        name="question"
                        value={draft}
                        onChange={(e) => {
                            setDraft(e.target.value);
                            if (formError) setFormError(null);
                        }}
                        onKeyDown={onKeyDown}
                        rows={1}
                        maxLength={config.maxLength + 50}
                        placeholder="Ask about experience, projects, leadership, or tech…"
                        aria-describedby={hintId}
                        aria-invalid={formError ? 'true' : undefined}
                        disabled={busy}
                        className="max-h-40 min-h-[28px] flex-1 resize-none bg-transparent py-0.5 font-sans text-[15.5px] leading-[1.6] text-ink outline-none placeholder:text-faint disabled:opacity-60"
                        style={{ fieldSizing: 'content' } as React.CSSProperties}
                    />
                    <button
                        type="submit"
                        disabled={busy || draft.trim() === ''}
                        aria-label={busy ? 'Waiting for the answer' : 'Send question'}
                        className="grid h-10 w-10 flex-none cursor-pointer place-items-center rounded-[12px] bg-accent text-on-accent transition-colors hover:bg-accent-bright disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        {busy ? <Spinner /> : <SendIcon />}
                    </button>
                </div>
                <div id={hintId} className="mt-2 flex items-center justify-between gap-4 text-[12.5px] text-faint">
                    <span>{formError ? <span className="text-danger">{formError}</span> : 'Enter to send · Shift+Enter for a new line'}</span>
                    <span aria-hidden="true" className={remaining < 60 ? 'text-danger' : ''}>
                        {remaining}
                    </span>
                </div>
            </form>
        </div>
    );
}

function EmptyState({ suggestions, onPick }: { suggestions: string[]; onPick: (q: string) => void }) {
    return (
        <div className="flex flex-col gap-6">
            <div className="flex items-start gap-3.5">
                <AssistantMark />
                <p className="pt-1 text-[15.5px] leading-[1.65] text-body">
                    Hi — I answer questions using what Miguel has documented in his portfolio. Try one of these, or ask your own.
                </p>
            </div>
            <div className="flex flex-wrap gap-2.5 min-[601px]:pl-[46px]">
                {suggestions.map((q) => (
                    <SuggestionChip key={q} label={q} onClick={() => onPick(q)} />
                ))}
            </div>
        </div>
    );
}

function SuggestionChip({ label, onClick, small = false }: { label: string; onClick: () => void; small?: boolean }) {
    return (
        <button
            type="button"
            onClick={onClick}
            className={`cursor-pointer rounded-full border border-line-strong bg-surface text-left font-medium text-muted transition-colors hover:border-accent/60 hover:text-ink ${
                small ? 'px-3 py-1.5 text-[12.5px]' : 'px-4 py-2 text-[13.5px]'
            }`}
        >
            {label}
        </button>
    );
}

function MessageBubble({ message, contactHref, onRetry }: { message: Message; contactHref: string; onRetry: () => void }) {
    if (message.role === 'user') {
        return (
            <div className="flex justify-end">
                <div className="max-w-[85%] whitespace-pre-wrap rounded-[18px] rounded-br-[6px] bg-accent/12 px-4 py-3 text-[15.5px] leading-[1.6] text-ink">
                    {message.content}
                </div>
            </div>
        );
    }

    const showThinking = message.pending && message.content === '';
    const showSources = !message.error && !message.pending && message.sources && message.sources.length > 0;
    const showContact = !message.error && !message.pending && (message.unanswered || message.content.includes(NO_INFORMATION_MARKER));

    return (
        <div className="flex items-start gap-3.5">
            <AssistantMark />
            <div className="min-w-0 flex-1">
                {showThinking ? (
                    <p className="flex items-center gap-2 pt-1.5 text-[14.5px] text-soft" aria-label="The assistant is thinking">
                        <ThinkingDots />
                        <span>Looking through Miguel's portfolio…</span>
                    </p>
                ) : message.error ? (
                    <div className="rounded-[16px] rounded-tl-[6px] border border-danger/40 bg-danger/10 px-4 py-3 text-[14.5px] leading-[1.6] text-danger">
                        {message.content}{' '}
                        <button type="button" onClick={onRetry} className="cursor-pointer font-semibold underline underline-offset-4">
                            Try again
                        </button>
                    </div>
                ) : (
                    <div className="text-[15.5px] leading-[1.7] text-body">
                        <Markdown text={message.content} />
                        {message.pending && <span aria-hidden="true" className="ml-0.5 inline-block h-[1.1em] w-[2px] translate-y-[3px] animate-pulse bg-accent" />}
                    </div>
                )}
                {showSources && (
                    <p className="mt-2.5 text-[12.5px] leading-[1.5] text-faint">
                        <span className="font-semibold text-soft">Based on:</span> {message.sources!.join(' · ')}
                    </p>
                )}
                {showContact && (
                    <p className="mt-2.5 text-[13px] text-faint">
                        <a href={contactHref} className="font-semibold text-accent underline-offset-4 hover:underline">
                            Ask Miguel directly →
                        </a>
                    </p>
                )}
            </div>
        </div>
    );
}

function AssistantMark() {
    return (
        <span
            aria-hidden="true"
            className="mt-0.5 grid h-8 w-8 flex-none place-items-center rounded-[10px] bg-accent/15 font-display text-[13px] font-bold text-accent"
        >
            MG
        </span>
    );
}

function ThinkingDots() {
    return (
        <span className="inline-flex items-center gap-1" aria-hidden="true">
            {[0, 1, 2].map((i) => (
                <span key={i} className="h-1.5 w-1.5 animate-bounce rounded-full bg-accent" style={{ animationDelay: `${i * 150}ms` }} />
            ))}
        </span>
    );
}

function Spinner() {
    return (
        <svg className="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="9" stroke="currentColor" strokeOpacity="0.3" strokeWidth="3" />
            <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" strokeWidth="3" strokeLinecap="round" />
        </svg>
    );
}

function SendIcon() {
    return (
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <path d="M12 19V5" />
            <path d="m5 12 7-7 7 7" />
        </svg>
    );
}
