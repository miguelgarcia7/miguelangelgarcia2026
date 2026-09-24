@php
    $suggestions = config('ai.suggested_questions');
@endphp

{{-- The "Ask about me" assistant, placed in the hero under the intro text.
     id="ask" keeps old links to /#ask landing on it. --}}
<div id="ask" class="mx-auto max-w-[720px] text-left">
    <h2 class="sr-only">Ask my AI assistant</h2>

    {{-- resources/js/ask/main.tsx replaces the contents of this element
         with the interactive assistant. What is inside is the no-JS
         version: the same questions, answered by the rest of the page. --}}
    <div
        data-ask-root
        data-endpoint="{{ route('ask') }}"
        data-suggestions='@json($suggestions)'
        data-max-length="{{ config('ai.conversation.max_question_length') }}"
        data-max-history="{{ config('ai.conversation.max_history') }}"
        data-contact-href="#contact"
    >
        <p class="mb-5 text-center text-[15.5px] leading-[1.65] text-body">
            My AI assistant answers questions like these from what I've documented. It needs JavaScript to run —
            with it off, the answers are in the sections below, or you can
            <a href="#contact" class="font-semibold text-accent underline-offset-4 hover:underline">ask me directly</a>.
        </p>
        <ul class="flex flex-wrap justify-center gap-2.5">
            @foreach (array_slice($suggestions, 0, 3) as $question)
                <li class="rounded-full border border-line-strong bg-surface px-4 py-2 text-[13.5px] font-medium text-muted">{{ $question }}</li>
            @endforeach
        </ul>
    </div>

    <p class="mt-4 text-center text-[12.5px] leading-[1.6] text-faint">
        AI answers from my own notes and may be imperfect. It won't invent experience — if something isn't documented, it says so.
    </p>
</div>
