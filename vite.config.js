import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Work Sans', {
                    weights: [400, 500, 600, 700],
                    optimizedFallbacks: false,
                }),
                bunny('Outfit', {
                    weights: [500, 600, 700],
                    optimizedFallbacks: false,
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            // Watching vendor/ and the SQLite database blocks the dev server on Windows.
            ignored: ['**/storage/**', '**/vendor/**', '**/bootstrap/cache/**', '**/database/**'],
        },
    },
});
