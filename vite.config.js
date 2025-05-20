import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/welcome.css',
                'resources/css/header.css',
                'resources/css/index.css',
                'resources/css/note-content.css',
                'resources/css/note-arbo.css',
            ],
            refresh: true,
        }),
    ],
});
