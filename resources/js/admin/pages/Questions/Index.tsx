import { Head, router } from '@inertiajs/react';
import { Badge, Group, Paper, SegmentedControl, SimpleGrid, Spoiler, Stack, Table, Text, Title } from '@mantine/core';
import { Paginator } from '../../components/Paginator';
import type { Paginated, QuestionLog } from '../../types';

interface Props {
    logs: Paginated<QuestionLog>;
    filter: 'all' | 'unanswered' | 'errors';
    stats: { total: number; unanswered: number; errors: number; cost: number };
}

const typeColors: Record<string, string> = {
    ABOUT_ME: 'mint',
    MIXED: 'blue',
    GENERAL_TECHNICAL: 'violet',
    OUT_OF_SCOPE: 'gray',
};

export default function Index({ logs, filter, stats }: Props) {
    return (
        <>
            <Head title="Questions" />
            <Group justify="space-between" align="flex-end" mb="lg">
                <div>
                    <Title order={2}>Questions</Title>
                    <Text c="dimmed" size="sm" mt={4}>
                        What visitors ask, and whether the knowledge base could answer. Unanswered questions are the best guide to what to write next.
                    </Text>
                </div>
                <SegmentedControl
                    value={filter}
                    onChange={(value) => router.get('/admin/ai-questions', value === 'all' ? {} : { filter: value }, { preserveState: true })}
                    data={[
                        { value: 'all', label: 'All' },
                        { value: 'unanswered', label: 'Unanswered' },
                        { value: 'errors', label: 'Errors' },
                    ]}
                />
            </Group>

            <SimpleGrid cols={{ base: 2, sm: 4 }} mb="lg">
                <Stat label="Questions" value={String(stats.total)} />
                <Stat label="Unanswered" value={String(stats.unanswered)} />
                <Stat label="Errors" value={String(stats.errors)} />
                <Stat label="Est. cost" value={`$${stats.cost.toFixed(4)}`} />
            </SimpleGrid>

            <Paper withBorder>
                <Table.ScrollContainer minWidth={900}>
                    <Table verticalSpacing="sm">
                        <Table.Thead>
                            <Table.Tr>
                                <Table.Th>When</Table.Th>
                                <Table.Th>Question &amp; answer</Table.Th>
                                <Table.Th>Type</Table.Th>
                                <Table.Th>Sources</Table.Th>
                                <Table.Th ta="right">Tokens</Table.Th>
                                <Table.Th ta="right">Cost</Table.Th>
                                <Table.Th ta="right">Time</Table.Th>
                            </Table.Tr>
                        </Table.Thead>
                        <Table.Tbody>
                            {logs.data.length === 0 && (
                                <Table.Tr>
                                    <Table.Td colSpan={7}>
                                        <Text c="dimmed" ta="center" py="xl">
                                            No questions yet.
                                        </Text>
                                    </Table.Td>
                                </Table.Tr>
                            )}
                            {logs.data.map((log) => (
                                <Table.Tr key={log.id}>
                                    <Table.Td>
                                        <Text size="xs" c="dimmed" style={{ whiteSpace: 'nowrap' }}>
                                            {log.created_at ? new Date(log.created_at).toLocaleString() : '—'}
                                        </Text>
                                    </Table.Td>
                                    <Table.Td style={{ maxWidth: 480 }}>
                                        <Stack gap={4}>
                                            <Text size="sm" fw={600}>
                                                {log.question}
                                            </Text>
                                            {log.error ? (
                                                <Text size="xs" c="red">
                                                    {log.error}
                                                </Text>
                                            ) : (
                                                <Spoiler maxHeight={40} showLabel="Show answer" hideLabel="Hide" fz="xs">
                                                    <Text size="xs" c="dimmed" style={{ whiteSpace: 'pre-wrap' }}>
                                                        {log.answer}
                                                    </Text>
                                                </Spoiler>
                                            )}
                                        </Stack>
                                    </Table.Td>
                                    <Table.Td>
                                        <Stack gap={4} align="flex-start">
                                            {log.question_type && (
                                                <Badge size="xs" variant="light" color={typeColors[log.question_type] ?? 'gray'}>
                                                    {log.question_type}
                                                </Badge>
                                            )}
                                            {!log.answered && !log.error && (
                                                <Badge size="xs" variant="outline" color="orange">
                                                    Unanswered
                                                </Badge>
                                            )}
                                        </Stack>
                                    </Table.Td>
                                    <Table.Td>
                                        <Text size="xs" c="dimmed">
                                            {log.sources.length ? log.sources.join(', ') : '—'}
                                        </Text>
                                    </Table.Td>
                                    <Table.Td ta="right">
                                        <Text size="xs" c="dimmed">
                                            {log.input_tokens} / {log.output_tokens}
                                        </Text>
                                    </Table.Td>
                                    <Table.Td ta="right">
                                        <Text size="xs" c="dimmed">
                                            ${log.estimated_cost.toFixed(4)}
                                        </Text>
                                    </Table.Td>
                                    <Table.Td ta="right">
                                        <Text size="xs" c="dimmed">
                                            {(log.duration_ms / 1000).toFixed(1)}s
                                        </Text>
                                    </Table.Td>
                                </Table.Tr>
                            ))}
                        </Table.Tbody>
                    </Table>
                </Table.ScrollContainer>
            </Paper>

            <Paginator page={logs} label="questions" />
        </>
    );
}

function Stat({ label, value }: { label: string; value: string }) {
    return (
        <Paper withBorder p="md">
            <Text size="xs" tt="uppercase" fw={700} c="dimmed" lts="0.08em">
                {label}
            </Text>
            <Text size="xl" fw={700} ff="'Space Grotesk', sans-serif">
                {value}
            </Text>
        </Paper>
    );
}
