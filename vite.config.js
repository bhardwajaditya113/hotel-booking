import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/nexstay-admin-tabler.css',
                'resources/js/app.js',
                'resources/js/portal-sync.js',
                'resources/js/booking-realtime.js',
            ],
            refresh: true,
        }),
    ],
});
