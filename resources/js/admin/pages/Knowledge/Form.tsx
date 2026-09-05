import { Head, Link, router, useForm } from '@inertiajs/react';
import {
    Anchor,
    Autocomplete,
    Badge,
    Button,
    Code,
    Divider,
    Grid,
    Group,
    Paper,
    Rating,
    SegmentedControl,
    Stack,
    Switch,
    Table,
    TagsInput,
    Text,
    Textarea,
    TextInput,
    Title,
} from '@mantine/core';
import { useState } from 'react';
import type { KnowledgeEntry, PreviewResult } from '../../types';

interface Props {
    entry: KnowledgeEntry | null;
    categories: string[];
}

const importanceLabels = ['', 'Minor detail', 'Useful context', 'Standard', 'Important', 'Core — always worth surfacing'];

export default function Form({ entry, categories }: Props) {
    const isEdit = entry !== null;

    const form = useForm({
        title: entry?.title ?? '',
        slug: entry?.slug ?? '',
        category: entry?.category ?? '',
        kind: entry?.kind ?? 'general',
        summary: entry?.summary ?? '',
        content: entry?.content ?? '',
        situation: entry?.situation ?? '',
        task: entry?.task ?? '',
        action: entry?.action ?? '',
        result: entry?.result ?? '',
        tags: entry?.tags ?? ([] as string[]),
        importance: entry?.importance ?? 3,
        is_active: entry?.is_active ?? true,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit) {
            form.put(`/admin/ai-knowledge/${entry.id}`, { preserveScroll: true });
        } else {
            form.post('/admin/ai-knowledge');
        }
    };

    const destroy = () => {
        if (!isEdit) return;
        if (!window.confirm(`Delete "${entry.title}"? This cannot be undone. Deactivating it keeps the text but hides it from the assistant.`)) return;
        router.delete(`/admin/ai-knowledge/${entry.id}`);
    };

    const isStar = form.data.kind === 'star';

    return (
        <>
            <Head title={isEdit ? `Edit: ${entry.title}` : 'New entry'} />
            <Group justify="space-between" align="flex-end" mb="lg">
                <div>
                    <Anchor component={Link} href="/admin/ai-knowledge" size="sm" c="dimmed">
                        ← Knowledge base
                    </Anchor>
                    <Title order={2} mt={4}>
                        {isEdit ? entry.title : 'New entry'}
                    </Title>
                </div>
                {isEdit && (
                    <Badge color={form.data.is_active ? 'mint' : 'gray'} variant="light" size="lg">
                        {form.data.is_active ? 'Active' : 'Inactive'}
                    </Badge>
                )}
            </Group>

            <Grid gap="lg">
                <Grid.Col span={{ base: 12, lg: 8 }}>
                    <form onSubmit={submit}>
                        <Paper withBorder p="lg">
                            <Stack gap="md">
                                <TextInput
                                    label="Title"
                                    description="How the entry is named for you and, as a heading, for the model."
                                    required
                                    value={form.data.title}
                                    onChange={(e) => form.setData('title', e.currentTarget.value)}
                                    error={form.errors.title}
                                />
                                <Group grow align="flex-start">
                                    <Autocomplete
                                        label="Category"
                                        description="Pick one or type a new one."
                                        required
                                        data={categories}
                                        value={form.data.category}
                                        onChange={(value) => form.setData('category', value)}
                                        error={form.errors.category}
                                    />
                                    <TextInput
                                        label="Slug"
                                        description="Optional; generated from the title."
                                        placeholder="auto"
                                        value={form.data.slug}
                                        onChange={(e) => form.setData('slug', e.currentTarget.value)}
                                        error={form.errors.slug}
                                    />
                                </Group>

                                <div>
                                    <Text size="sm" fw={500} mb={4}>
                                        Entry type
                                    </Text>
                                    <SegmentedControl
                                        value={form.data.kind}
                                        onChange={(value) => form.setData('kind', value as 'general' | 'star')}
                                        data={[
                                            { value: 'general', label: 'General knowledge' },
                                            { value: 'star', label: 'STAR story' },
                                        ]}
                                    />
                                    <Text size="xs" c="dimmed" mt={4}>
                                        {isStar
                                            ? 'An interview-style story. One story can answer many questions — tag it with every theme it illustrates.'
                                            : 'A fact, topic, or description written in your own words.'}
                                    </Text>
                                </div>

                                <Textarea
                                    label="Summary"
                                    description="One or two sentences. Shown to the model above the body and used heavily for matching questions."
                                    autosize
                                    minRows={2}
                                    value={form.data.summary}
                                    onChange={(e) => form.setData('summary', e.currentTarget.value)}
                                    error={form.errors.summary}
                                />

                                {isStar ? (
                                    <>
                                        <Divider label="STAR" labelPosition="left" />
                                        {(['situation', 'task', 'action', 'result'] as const).map((field) => (
                                            <Textarea
                                                key={field}
                                                label={starLabels[field].label}
                                                description={starLabels[field].hint}
                                                required
                                                autosize
                                                minRows={3}
                                                value={form.data[field]}
                                                onChange={(e) => form.setData(field, e.currentTarget.value)}
                                                error={form.errors[field]}
                                            />
                                        ))}
                                    </>
                                ) : (
                                    <Textarea
                                        label="Content"
                                        description="Write in the third person or first person — the assistant restates it either way. Be specific: names, numbers, years, tools."
                                        required
                                        autosize
                                        minRows={8}
                                        value={form.data.content}
                                        onChange={(e) => form.setData('content', e.currentTarget.value)}
                                        error={form.errors.content}
                                    />
                                )}

                                <TagsInput
                                    label="Tags"
                                    description="Lower-case keywords a visitor might use: technologies, themes, soft skills. Press Enter after each."
                                    value={form.data.tags}
                                    onChange={(value) => form.setData('tags', value)}
                                    error={form.errors.tags}
                                    clearable
                                />

                                <Group align="flex-start" grow>
                                    <div>
                                        <Text size="sm" fw={500}>
                                            Importance
                                        </Text>
                                        <Group gap="sm" mt={4}>
                                            <Rating value={form.data.importance} onChange={(value) => form.setData('importance', value)} />
                                            <Text size="xs" c="dimmed">
                                                {importanceLabels[form.data.importance]}
                                            </Text>
                                        </Group>
                                        {form.errors.importance && (
                                            <Text size="xs" c="red">
                                                {form.errors.importance}
                                            </Text>
                                        )}
                                    </div>
                                    <Switch
                                        label="Active"
                                        description="Inactive entries are kept but never sent to the model."
                                        checked={form.data.is_active}
                                        onChange={(e) => form.setData('is_active', e.currentTarget.checked)}
                                        mt={4}
                                    />
                                </Group>
                            </Stack>

                            <Group justify="space-between" mt="xl">
                                <Group>
                                    <Button type="submit" loading={form.processing}>
                                        {isEdit ? 'Save changes' : 'Create entry'}
                                    </Button>
                                    {form.recentlySuccessful && (
                                        <Text size="sm" c="mint.4">
                                            Saved.
                                        </Text>
                                    )}
                                </Group>
                                {isEdit && (
                                    <Button variant="subtle" color="red" onClick={destroy}>
                                        Delete
                                    </Button>
                                )}
                            </Group>
                        </Paper>
                    </form>
                </Grid.Col>

                <Grid.Col span={{ base: 12, lg: 4 }}>
                    {isEdit ? (
                        <PreviewPanel entry={entry} />
                    ) : (
                        <Paper withBorder p="lg">
                            <Title order={5} mb="xs">
                                Writing tips
                            </Title>
                            <Stack gap="xs">
                                <Text size="sm" c="dimmed">
                                    The assistant only says what is written here. If a fact is missing, it tells the visitor it is not documented.
                                </Text>
                                <Text size="sm" c="dimmed">
                                    Keep one topic per entry. "Laravel experience" and "Stripe integrations" retrieve better as two entries than one long
                                    one.
                                </Text>
                                <Text size="sm" c="dimmed">
                                    Tags and summary drive retrieval. Add the words a recruiter would actually type.
                                </Text>
                                <Text size="sm" c="dimmed">
                                    After saving, the preview panel shows exactly what the model sees and where the entry ranks for a sample question.
                                </Text>
                            </Stack>
                        </Paper>
                    )}
                </Grid.Col>
            </Grid>
        </>
    );
}

const starLabels = {
    situation: { label: 'Situation', hint: 'The context: company, project, constraints, what was at stake.' },
    task: { label: 'Task', hint: 'What you were responsible for.' },
    action: { label: 'Action', hint: 'What you personally did, step by step.' },
    result: { label: 'Result', hint: 'The outcome, with numbers where you have them.' },
} as const;

function PreviewPanel({ entry }: { entry: KnowledgeEntry }) {
    const [question, setQuestion] = useState('');
    const [result, setResult] = useState<PreviewResult | null>(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const run = async (q: string) => {
        setLoading(true);
        setError(null);

        try {
            const token = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
            const response = await fetch(`/admin/ai-knowledge/${entry.id}/preview`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ question: q }),
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            setResult((await response.json()) as PreviewResult);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Preview failed');
        } finally {
            setLoading(false);
        }
    };

    return (
        <Stack gap="md">
            <Paper withBorder p="lg">
                <Title order={5} mb={4}>
                    Test retrieval
                </Title>
                <Text size="sm" c="dimmed" mb="sm">
                    Ask something a visitor might ask and see whether this entry would be sent to the model. Uses the saved version of the entry.
                </Text>
                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        void run(question);
                    }}
                >
                    <TextInput
                        placeholder="e.g. Tell me about a difficult deadline"
                        value={question}
                        onChange={(e) => setQuestion(e.currentTarget.value)}
                        rightSectionWidth={80}
                        rightSection={
                            <Button type="submit" size="xs" loading={loading} disabled={question.trim() === ''}>
                                Run
                            </Button>
                        }
                    />
                </form>
                {error && (
                    <Text size="sm" c="red" mt="sm">
                        {error}
                    </Text>
                )}

                {result && result.classification && (
                    <Stack gap="xs" mt="md">
                        <Group gap="xs">
                            <Badge color={result.would_be_used ? 'mint' : 'red'} variant="light">
                                {result.would_be_used ? 'Would be used' : result.is_active ? 'Would not be used' : 'Inactive — never used'}
                            </Badge>
                            <Badge variant="outline" color="gray">
                                {result.classification.type}
                            </Badge>
                            <Text size="xs" c="dimmed">
                                classified by {result.classification_source === 'model' ? 'the model' : 'keywords only'}
                            </Text>
                        </Group>
                        {result.classification.standalone_question && (
                            <Text size="xs" c="dimmed">
                                Rewritten: “{result.classification.standalone_question}”
                            </Text>
                        )}
                        <Text size="xs" c="dimmed">
                            Search terms: {result.classification.search_terms.join(', ') || '—'}
                            {result.classification.categories.length > 0 && ` · Categories: ${result.classification.categories.join(', ')}`}
                        </Text>
                        <Table verticalSpacing={4} fz="xs">
                            <Table.Thead>
                                <Table.Tr>
                                    <Table.Th>#</Table.Th>
                                    <Table.Th>Entry</Table.Th>
                                    <Table.Th ta="right">Score</Table.Th>
                                </Table.Tr>
                            </Table.Thead>
                            <Table.Tbody>
                                {result.ranking.map((row, i) => (
                                    <Table.Tr key={row.id} style={{ opacity: row.selected ? 1 : 0.55 }}>
                                        <Table.Td>{i + 1}</Table.Td>
                                        <Table.Td fw={row.is_this ? 700 : 400} c={row.is_this ? 'mint.4' : undefined}>
                                            {row.title}
                                            {row.selected && ' ✓'}
                                        </Table.Td>
                                        <Table.Td ta="right" title={Object.entries(row.breakdown).map(([k, v]) => `${k}: ${v}`).join('\n')}>
                                            {row.score.toFixed(2)}
                                        </Table.Td>
                                    </Table.Tr>
                                ))}
                                {result.ranking.every((r) => r.score === 0) && (
                                    <Table.Tr>
                                        <Table.Td colSpan={3}>
                                            <Text size="xs" c="dimmed">
                                                Nothing matched — the assistant would say this is not documented.
                                            </Text>
                                        </Table.Td>
                                    </Table.Tr>
                                )}
                            </Table.Tbody>
                        </Table>
                    </Stack>
                )}
            </Paper>

            <Paper withBorder p="lg">
                <Title order={5} mb={4}>
                    What the model sees
                </Title>
                <Text size="sm" c="dimmed" mb="sm">
                    The saved entry, exactly as it is placed inside the context block.
                </Text>
                {result ? (
                    <Code block style={{ whiteSpace: 'pre-wrap', maxHeight: 360, overflow: 'auto' }}>
                        {result.prompt_text}
                    </Code>
                ) : (
                    <Button variant="light" size="xs" onClick={() => void run('')} loading={loading}>
                        Show
                    </Button>
                )}
            </Paper>
        </Stack>
    );
}
