import { createRoot } from 'react-dom/client';
import { AskAssistant } from './AskAssistant';
import type { AskConfig } from './types';

/**
 * Mounts the assistant into the Blade-rendered #ask section. The server
 * renders a static fallback in the same element, so the page reads fine
 * before this runs and when JavaScript is off.
 */
const root = document.querySelector<HTMLElement>('[data-ask-root]');

if (root) {
    let suggestions: string[] = [];
    try {
        suggestions = JSON.parse(root.dataset.suggestions ?? '[]');
    } catch {
        suggestions = [];
    }

    const config: AskConfig = {
        endpoint: root.dataset.endpoint ?? '/ask',
        csrfToken: document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '',
        suggestions,
        maxLength: Number(root.dataset.maxLength ?? 600),
        maxHistory: Number(root.dataset.maxHistory ?? 6),
        contactHref: root.dataset.contactHref ?? '#contact',
    };

    createRoot(root).render(<AskAssistant config={config} />);
}
