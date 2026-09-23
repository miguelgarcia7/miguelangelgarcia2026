/**
 * Reads the server-sent event stream produced by POST /ask.
 *
 * EventSource only supports GET, so the stream is read from a fetch body
 * and split into "event:" / "data:" frames by hand.
 */
export interface StreamHandlers {
    onMeta?: (meta: { type: string; sources: string[] }) => void;
    onDelta: (text: string) => void;
    onDone?: (done: { answered: boolean }) => void;
    onError: (message: string) => void;
}

export class AskRequestError extends Error {
    constructor(
        message: string,
        public readonly status: number,
    ) {
        super(message);
    }
}

export async function askStream(
    endpoint: string,
    csrfToken: string,
    body: { question: string; history: { role: string; content: string }[] },
    handlers: StreamHandlers,
    signal?: AbortSignal,
): Promise<void> {
    const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'text/event-stream',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(body),
        signal,
    });

    if (!response.ok) {
        let message = 'Something went wrong. Please try again.';

        try {
            const payload = await response.json();
            if (typeof payload?.message === 'string' && payload.message) message = payload.message;
        } catch {
            // Not JSON — keep the generic message.
        }

        if (response.status === 429) {
            message = 'You’ve asked a lot of questions in a short time — please wait a minute and try again.';
        }

        throw new AskRequestError(message, response.status);
    }

    if (!response.body) {
        throw new AskRequestError('The connection closed before an answer arrived.', 0);
    }

    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    let buffer = '';

    const dispatch = (frame: string) => {
        let event = 'message';
        const data: string[] = [];

        for (const line of frame.split('\n')) {
            if (line.startsWith('event:')) event = line.slice(6).trim();
            else if (line.startsWith('data:')) data.push(line.slice(5).replace(/^ /, ''));
        }

        if (data.length === 0) return;

        let payload: unknown;
        try {
            payload = JSON.parse(data.join('\n'));
        } catch {
            return;
        }

        const record = (payload ?? {}) as Record<string, unknown>;

        switch (event) {
            case 'meta':
                handlers.onMeta?.({
                    type: String(record.type ?? ''),
                    sources: Array.isArray(record.sources) ? record.sources.map(String) : [],
                });
                break;
            case 'delta':
                if (typeof record.text === 'string') handlers.onDelta(record.text);
                break;
            case 'done':
                handlers.onDone?.({ answered: record.answered !== false });
                break;
            case 'error':
                handlers.onError(typeof record.message === 'string' ? record.message : 'Something went wrong.');
                break;
        }
    };

    for (;;) {
        const { value, done } = await reader.read();
        if (done) break;

        buffer += decoder.decode(value, { stream: true });

        let boundary = buffer.indexOf('\n\n');
        while (boundary !== -1) {
            dispatch(buffer.slice(0, boundary));
            buffer = buffer.slice(boundary + 2);
            boundary = buffer.indexOf('\n\n');
        }
    }

    if (buffer.trim()) dispatch(buffer);
}
