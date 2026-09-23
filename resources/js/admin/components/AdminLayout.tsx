import { Link, router, usePage } from '@inertiajs/react';
import { Alert, Anchor, AppShell, Button, Container, Group, Text, Title } from '@mantine/core';
import { useEffect, useState, type ReactNode } from 'react';
import type { SharedProps } from '../types';

const links = [
    { href: '/admin/ai-knowledge', label: 'Knowledge base', match: '/admin/ai-knowledge' },
    { href: '/admin/ai-questions', label: 'Questions', match: '/admin/ai-questions' },
];

export function AdminLayout({ children }: { children: ReactNode }) {
    const { auth, flash } = usePage<SharedProps>().props;
    const path = typeof window !== 'undefined' ? window.location.pathname : '';

    return (
        <AppShell header={{ height: 64 }} padding="lg">
            <AppShell.Header withBorder>
                <Container size="xl" h="100%">
                    <Group h="100%" justify="space-between">
                        <Group gap="xl">
                            <Title order={4} ff="'Space Grotesk', sans-serif" lts="-0.02em">
                                <Text span c="mint.4" inherit>
                                    MG
                                </Text>{' '}
                                Admin
                            </Title>
                            <Group gap="md" visibleFrom="sm">
                                {links.map((link) => (
                                    <Anchor
                                        key={link.href}
                                        component={Link}
                                        href={link.href}
                                        fw={600}
                                        size="sm"
                                        c={path.startsWith(link.match) ? 'mint.4' : 'dimmed'}
                                        underline="never"
                                    >
                                        {link.label}
                                    </Anchor>
                                ))}
                            </Group>
                        </Group>
                        <Group gap="sm">
                            <Anchor href="/" size="sm" c="dimmed" underline="never" target="_blank" rel="noopener">
                                View site ↗
                            </Anchor>
                            {auth.user && (
                                <Button variant="subtle" color="gray" size="xs" onClick={() => router.post('/admin/logout')}>
                                    Sign out
                                </Button>
                            )}
                        </Group>
                    </Group>
                </Container>
            </AppShell.Header>
            <AppShell.Main>
                <Container size="xl">
                    <FlashMessages success={flash.success} error={flash.error} />
                    {children}
                </Container>
            </AppShell.Main>
        </AppShell>
    );
}

function FlashMessages({ success, error }: { success: string | null; error: string | null }) {
    const [visible, setVisible] = useState(true);

    // A new flash message should show even if the previous one was closed.
    useEffect(() => setVisible(true), [success, error]);

    if (!visible || (!success && !error)) return null;

    return (
        <Alert
            mb="lg"
            color={error ? 'red' : 'mint'}
            variant="light"
            withCloseButton
            onClose={() => setVisible(false)}
            title={error ? 'Something went wrong' : undefined}
        >
            {error ?? success}
        </Alert>
    );
}
