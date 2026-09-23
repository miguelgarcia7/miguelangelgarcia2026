import { router } from '@inertiajs/react';
import { Group, Pagination, Text } from '@mantine/core';
import type { Paginated } from '../types';

export function Paginator({ page, label }: { page: Paginated<unknown>; label: string }) {
    if (page.last_page <= 1) {
        return (
            <Text size="sm" c="dimmed" mt="md">
                {page.total} {label}
            </Text>
        );
    }

    const go = (target: number) => {
        const url = new URL(window.location.href);
        url.searchParams.set('page', String(target));
        router.get(url.pathname + url.search, {}, { preserveState: true, preserveScroll: true });
    };

    return (
        <Group justify="space-between" mt="md">
            <Text size="sm" c="dimmed">
                {page.from}–{page.to} of {page.total} {label}
            </Text>
            <Pagination value={page.current_page} total={page.last_page} onChange={go} size="sm" />
        </Group>
    );
}
