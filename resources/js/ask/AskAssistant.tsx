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
    const placeholder = useRotatingPlaceholder(config.suggestions, draft === '');

    return (
        <div>
            {/* Composer */}
            <form onSubmit={onSubmit}>
                <label htmlFor={inputId} className="sr-only">
                    Ask a question about Miguel
                </label>
                {/* One focus indicator for the whole composer: the wrapper's
                    border turns accent on focus-within, and the textarea's own
                    focus ring is turned off in app.css so the two do not nest. */}
                <div
                    className={`flex items-center gap-3 rounded-[20px] border-2 bg-bg/85 py-2 pl-4 pr-2 transition-[border-color,box-shadow] focus-within:border-accent focus-within:shadow-[0_0_0_6px_rgb(46_230_166/0.08)] min-[601px]:py-3 min-[601px]:pl-5 min-[601px]:pr-3 ${
                        formError ? 'border-danger' : 'border-line-control'
                    }`}
                >
                    <SparkIcon />
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
                        placeholder={placeholder}
                        aria-describedby={hintId}
                        aria-invalid={formError ? 'true' : undefined}
                        disabled={busy}
                        className="max-h-40 min-h-[28px] flex-1 resize-none bg-transparent py-1 font-sans text-[16px] leading-[1.6] text-ink outline-none placeholder:text-faint disabled:opacity-60 min-[601px]:text-[18px]"
                        style={{ fieldSizing: 'content' } as React.CSSProperties}
                    />
                    <button
                        type="submit"
                        disabled={busy || draft.trim() === ''}
                        aria-label={busy ? 'Waiting for the answer' : 'Send question'}
                        className="grid h-11 w-11 flex-none cursor-pointer place-items-center rounded-[14px] bg-accent text-on-accent transition-colors hover:bg-accent-bright disabled:cursor-not-allowed disabled:opacity-40 min-[601px]:h-12 min-[601px]:w-12"
                    >
                        {busy ? <Spinner /> : <SendIcon />}
                    </button>
                </div>
                <div id={hintId} className="mt-2 flex min-h-[20px] items-center justify-between gap-4 px-1 text-[12.5px] text-faint">
                    {formError ? <span className="text-danger">{formError}</span> : <span className="sr-only">Enter to send, Shift+Enter for a new line.</span>}
                    {remaining < 100 && (
                        <span aria-hidden="true" className={`ml-auto ${remaining < 60 ? 'text-danger' : ''}`}>
                            {remaining}
                        </span>
                    )}
                </div>
            </form>

            {isEmpty && (
                <div className="mt-2 flex flex-wrap justify-center gap-2.5">
                    {config.suggestions.slice(0, 3).map((q) => (
                        <SuggestionChip key={q} label={q} onClick={() => void ask(q)} />
                    ))}
                </div>
            )}

            {/* Transcript. Always rendered so the live region exists before the
                first answer arrives; it only takes on the card look once there
                is something in it. */}
            <div className={isEmpty ? '' : 'mt-4 overflow-hidden rounded-[22px] border border-line bg-surface-deep shadow-[0_30px_80px_-40px_rgba(0,0,0,0.8)]'}>
                <div
                    ref={logRef}
                    role="log"
                    aria-live="polite"
                    aria-relevant="additions text"
                    aria-label="Conversation with the assistant"
                    className={isEmpty ? '' : 'flex max-h-[min(52vh,520px)] flex-col gap-5 overflow-y-auto px-5 py-6 min-[601px]:px-7'}
                >
                    {messages.map((message) => (
                        <MessageBubble key={message.id} message={message} contactHref={config.contactHref} onRetry={() => retry(message.id)} />
                    ))}
                </div>

                {/* Follow-up suggestions once a conversation has started */}
                {!isEmpty && !busy && (
                    <div className="flex flex-wrap items-center gap-2 border-t border-line px-5 py-3 min-[601px]:px-7">
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
            </div>
        </div>
    );
}

const DEFAULT_PLACEHOLDER = 'Ask me anything about my work…';

/**
 * Cycles the composer's placeholder through the suggested questions while
 * the field is empty. Wide screens only: a long question would wrap on a
 * phone and make the auto-sizing field jump in height.
 */
function useRotatingPlaceholder(suggestions: string[], active: boolean): string {
    const [index, setIndex] = useState(-1);

    useEffect(() => {
        if (!active || suggestions.length === 0 || !window.matchMedia('(min-width: 601px)').matches) return;
        const timer = window.setInterval(() => setIndex((i) => (i + 1) % suggestions.length), 3200);
        return () => window.clearInterval(timer);
    }, [active, suggestions.length]);

    return index < 0 ? DEFAULT_PLACEHOLDER : suggestions[index];
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

function SparkIcon() {
    return (
        <svg className="h-5 w-5 flex-none text-accent" viewBox="0 0 24 24" aria-hidden="true">
            <path
                fill="currentColor"
                d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8zM19 16l.8 2.2L22 19l-2.2.8L19 22l-.8-2.2L16 19l2.2-.8z"
            />
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
