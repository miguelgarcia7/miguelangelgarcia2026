import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Public site: Blade + Tailwind + a little vanilla JS.
                'resources/css/app.css',
                'resources/js/app.js',
                // "Ask about me": one React island mounted into the Blade page.
                'resources/js/ask/main.tsx',
                // Admin: Inertia + React + Mantine, client-rendered only.
                'resources/css/admin.css',
                'resources/js/admin/app.tsx',
            ],
            refresh: true,
            fonts: [
                bunny('Space Grotesk', {
                    weights: [400, 500, 600, 700],
                }),
                bunny('Manrope', {
                    weights: [400, 500, 600, 700, 800],
                }),
            ],
        }),
        react(),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
