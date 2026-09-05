import { Fragment, type ReactNode } from 'react';

/**
 * Renders the light Markdown the assistant is asked to use: paragraphs,
 * bullet lists, **bold**, *italic*, and `code`. Everything is built as React
 * nodes from the text, so nothing the model writes is treated as HTML.
 */
export function Markdown({ text }: { text: string }) {
    const blocks = text.replace(/\r\n/g, '\n').split(/\n{2,}/);

    return (
        <>
            {blocks.map((block, i) => {
                const lines = block.split('\n').filter((line) => line.trim() !== '');
                if (lines.length === 0) return null;

                const isList = lines.every((line) => /^\s*(?:[-*•]|\d+[.)])\s+/.test(line));

                if (isList) {
                    const ordered = /^\s*\d+[.)]\s+/.test(lines[0]);
                    const Tag = ordered ? 'ol' : 'ul';

                    return (
                        <Tag key={i} className={`my-2 space-y-1 pl-5 ${ordered ? 'list-decimal' : 'list-disc'} marker:text-accent`}>
                            {lines.map((line, j) => (
                                <li key={j}>{inline(line.replace(/^\s*(?:[-*•]|\d+[.)])\s+/, ''))}</li>
                            ))}
                        </Tag>
                    );
                }

                return (
                    <p key={i} className="my-2 first:mt-0 last:mb-0">
                        {lines.map((line, j) => (
                            <Fragment key={j}>
                                {j > 0 && <br />}
                                {inline(line)}
                            </Fragment>
                        ))}
                    </p>
                );
            })}
        </>
    );
}

function inline(text: string): ReactNode[] {
    const nodes: ReactNode[] = [];
    const pattern = /(\*\*[^*]+\*\*|`[^`]+`|\*[^*\n]+\*)/g;
    let last = 0;
    let match: RegExpExecArray | null;
    let key = 0;

    while ((match = pattern.exec(text)) !== null) {
        if (match.index > last) nodes.push(text.slice(last, match.index));

        const token = match[0];
        if (token.startsWith('**')) {
            nodes.push(
                <strong key={key++} className="font-semibold text-ink">
                    {token.slice(2, -2)}
                </strong>,
            );
        } else if (token.startsWith('`')) {
            nodes.push(
                <code key={key++} className="rounded-md bg-white/8 px-1.5 py-0.5 font-mono text-[0.9em] text-ink">
                    {token.slice(1, -1)}
                </code>,
            );
        } else {
            nodes.push(<em key={key++}>{token.slice(1, -1)}</em>);
        }

        last = match.index + token.length;
    }

    if (last < text.length) nodes.push(text.slice(last));

    return nodes;
}
