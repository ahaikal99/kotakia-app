import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { readdirSync } from 'node:fs';

const designAssets = ['css', 'js'].flatMap((extension) =>
    readdirSync(new URL(`./resources/${extension}/designs/`, import.meta.url), { withFileTypes: true })
        .filter((entry) => entry.isFile() && new RegExp(`^[a-z][a-z0-9_-]*\\.${extension}$`).test(entry.name))
        .map((entry) => `resources/${extension}/designs/${entry.name}`)
);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', ...designAssets],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
        },
    },
});
