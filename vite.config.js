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
                'resources/css/note-search.css',
                'resources/css/note-toolbar.css',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '127.0.0.1',
        port: 5173,
    },
});
