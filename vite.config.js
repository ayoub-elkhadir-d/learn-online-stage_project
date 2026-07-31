import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/auth.css',
                'resources/css/admin.css',
                'resources/css/learn.css',
                'resources/css/course-details.css',
                'resources/css/dashboard.css',
                'resources/css/courses-index.css',
                'resources/js/app.js',
                'resources/js/learn.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
