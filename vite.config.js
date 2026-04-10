import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import fg from 'fast-glob';
import laravel from 'laravel-vite-plugin';

const inputs = [
    'resources/assets/js/app.js',
    'resources/assets/css/style.css',
    ...fg.sync([
        'resources/assets/scss/**/*.scss',
        'resources/assets/images/**/*.{png,jpg,jpeg,svg,webp,avif,gif,mp4,webm}',
        'resources/assets/svg/**/*.svg',
    ], { onlyFiles: true }),
];

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: inputs,
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: Number(process.env.VITE_PORT ?? 5173),
        strictPort: true,
        hmr: {
            host: 'localhost',
            clientPort: Number(process.env.VITE_PORT ?? 5173),
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
