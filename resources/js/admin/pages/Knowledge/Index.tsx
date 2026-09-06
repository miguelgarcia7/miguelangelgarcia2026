import { Head, Link, router } from '@inertiajs/react';
import {
    Anchor,
    Badge,
    Button,
    Group,
    Paper,
    Pill,
    Rating,
    SegmentedControl,
    Select,
    SimpleGrid,
    Stack,
    Switch,
    Table,
    Text,
    TextInput,
    Title,
} from '@mantine/core';
import { useEffect, useState } from 'react';
import { Paginator } from '../../components/Paginator';
import type { KnowledgeRow, Paginated } from '../../types';

interface Props {
    entries: Paginated<KnowledgeRow>;
    filters: { search: string; category: string; status: 'all' | 'active' | 'inactive'; kind: '' | 'general' | 'star' };
    categories: string[];
    stats: { total: number; active: number; star: number };
}

export default function Index({ entries, filters, categories, stats }: Props) {
    const [search, setSearch] = useState(filters.search);

    const apply = (patch: Partial<Props['filters']>) => {
        const next = { ...filters, search, ...patch };
        const query = Object.fromEntries(Object.entries(next).filter(([, v]) => v && v !== 'all'));
        router.get('/admin/ai-knowledge', query, { preserveState: true, replace: true });
    };

    // Debounce the search box so every keystroke is not a request.
    useEffect(() => {
        if (search === filters.search) return;
        const handle = window.setTimeout(() => apply({ search }), 350);
        return () => window.clearTimeout(handle);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    const toggle = (row: KnowledgeRow) =>
        router.patch(`/admin/ai-knowledge/${row.id}/toggle`, {}, { preserveScroll: true, preserveState: true });

    return (
        <>
            <Head title="Knowledge base" />
            <Group justify="space-between" align="flex-end" mb="lg">
                <div>
                    <Title order={2}>Knowledge base</Title>
                    <Text c="dimmed" size="sm" mt={4}>
                        Everything the assistant is allowed to say about you. Only active entries are ever sent to the model.
                    </Text>
                </div>
                <Button component={Link} href="/admin/ai-knowledge/create">
                    New entry
                </Button>
            </Group>

            <SimpleGrid cols={{ base: 1, sm: 3 }} mb="lg">
                <Stat label="Entries" value={stats.total} />
                <Stat label="Active" value={stats.active} hint={stats.total - stats.active > 0 ? `${stats.total - stats.active} inactive` : undefined} />
                <Stat label="STAR stories" value={stats.star} />
            </SimpleGrid>

            <Paper withBorder p="md" mb="md">
                <Group align="flex-end" wrap="wrap">
                    <TextInput
                        label="Search"
                        placeholder="Title, summary, content, tags"
                        value={search}
                        onChange={(e) => setSearch(e.currentTarget.value)}
                        style={{ flex: 1, minWidth: 220 }}
                    />
                    <Select
                        label="Category"
                        placeholder="Any"
                        clearable
                        searchable
                        data={categories}
                        value={filters.category || null}
                        onChange={(value) => apply({ category: value ?? '' })}
                        w={220}
                    />
                    <Select
                        label="Type"
                        placeholder="Any"
                        clearable
                        data={[
                            { value: 'general', label: 'General' },
                            { value: 'star', label: 'STAR story' },
                        ]}
                        value={filters.kind || null}
                        onChange={(value) => apply({ kind: (value as Props['filters']['kind']) ?? '' })}
                        w={160}
                    />
                    <div>
                        <Text size="sm" fw={500} mb={4}>
                            Status
                        </Text>
                        <SegmentedControl
                            value={filters.status}
                            onChange={(value) => apply({ status: value as Props['filters']['status'] })}
                            data={[
                                { value: 'all', label: 'All' },
                                { value: 'active', label: 'Active' },
                                { value: 'inactive', label: 'Inactive' },
                            ]}
                        />
                    </div>
                </Group>
            </Paper>

            <Paper withBorder>
                <Table.ScrollContainer minWidth={860}>
                    <Table verticalSpacing="sm" highlightOnHover>
                        <Table.Thead>
                            <Table.Tr>
                                <Table.Th>Entry</Table.Th>
                                <Table.Th>Categories</Table.Th>
                                <Table.Th>Tags</Table.Th>
                                <Table.Th>Importance</Table.Th>
                                <Table.Th>Active</Table.Th>
                                <Table.Th>Updated</Table.Th>
                            </Table.Tr>
                        </Table.Thead>
                        <Table.Tbody>
                            {entries.data.length === 0 && (
                                <Table.Tr>
                                    <Table.Td colSpan={6}>
                                        <Text c="dimmed" ta="center" py="xl">
                                            No entries match. {stats.total === 0 && 'Create your first entry, or run the seeder for starter content.'}
                                        </Text>
                                    </Table.Td>
                                </Table.Tr>
                            )}
                            {entries.data.map((row) => (
                                <Table.Tr key={row.id} style={{ opacity: row.is_active ? 1 : 0.55 }}>
                                    <Table.Td>
                                        <Stack gap={2}>
                                            <Group gap="xs">
                                                <Anchor component={Link} href={`/admin/ai-knowledge/${row.id}/edit`} fw={600}>
                                                    {row.title}
                                                </Anchor>
                                                {row.kind === 'star' && (
                                                    <Badge size="xs" variant="light" color="violet">
                                                        STAR
                                                    </Badge>
                                                )}
                                            </Group>
                                            {row.summary && (
                                                <Text size="xs" c="dimmed" lineClamp={1}>
                                                    {row.summary}
                                                </Text>
                                            )}
                                        </Stack>
                                    </Table.Td>
                                    <Table.Td>
                                        <Group gap={4}>
                                            {row.categories.map((category) => (
                                                <Badge key={category} variant="outline" color="gray" size="sm">
                                                    {category}
                                                </Badge>
                                            ))}
                                        </Group>
                                    </Table.Td>
                                    <Table.Td>
                                        <Group gap={4}>
                                            {row.tags.slice(0, 4).map((tag) => (
                                                <Pill key={tag} size="xs">
                                                    {tag}
                                                </Pill>
                                            ))}
                                            {row.tags.length > 4 && (
                                                <Text size="xs" c="dimmed">
                                                    +{row.tags.length - 4}
                                                </Text>
                                            )}
                                        </Group>
                                    </Table.Td>
                                    <Table.Td>
                                        <Rating value={row.importance} readOnly size="xs" />
                                    </Table.Td>
                                    <Table.Td>
                                        <Switch checked={row.is_active} onChange={() => toggle(row)} aria-label={`Toggle ${row.title}`} size="sm" />
                                    </Table.Td>
                                    <Table.Td>
                                        <Text size="xs" c="dimmed">
                                            {row.updated_at ? new Date(row.updated_at).toLocaleDateString() : '—'}
                                        </Text>
                                    </Table.Td>
                                </Table.Tr>
                            ))}
                        </Table.Tbody>
                    </Table>
                </Table.ScrollContainer>
            </Paper>

            <Paginator page={entries} label="entries" />
        </>
    );
}

function Stat({ label, value, hint }: { label: string; value: number; hint?: string }) {
    return (
        <Paper withBorder p="md">
            <Text size="xs" tt="uppercase" fw={700} c="dimmed" lts="0.08em">
                {label}
            </Text>
            <Group align="baseline" gap="xs">
                <Text size="xl" fw={700} ff="'Space Grotesk', sans-serif">
                    {value}
                </Text>
                {hint && (
                    <Text size="xs" c="dimmed">
                        {hint}
                    </Text>
                )}
            </Group>
        </Paper>
    );
}
