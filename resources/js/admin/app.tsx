import { createInertiaApp } from '@inertiajs/react';
import { MantineProvider } from '@mantine/core';
import { createRoot } from 'react-dom/client';
import type { ComponentType, ReactNode } from 'react';
import { AdminLayout } from './components/AdminLayout';
import { theme } from './theme';

type PageModule = { default: ComponentType & { layout?: (page: ReactNode) => ReactNode } };

const pages = import.meta.glob<PageModule>('./pages/**/*.tsx', { eager: true });

void createInertiaApp({
    resolve: (name) => {
        const page = pages[`./pages/${name}.tsx`];
        if (!page) throw new Error(`Admin page "${name}" not found.`);

        // Every page shares the admin shell unless it opts out.
        page.default.layout ??= (content) => <AdminLayout>{content}</AdminLayout>;

        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(
            <MantineProvider theme={theme} forceColorScheme="dark">
                <App {...props} />
            </MantineProvider>,
        );
    },
    progress: { color: '#2ee6a6' },
});
