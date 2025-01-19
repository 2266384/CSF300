import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import mkcert from "vite-plugin-mkcert";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        mkcert(),
    ],
    server: {
        https: true,
        host: '0.0.0.0',
        port: 5174
    },
});


