import { defineConfig } from 'vite';
import fg from 'fast-glob';
import laravel from 'laravel-vite-plugin';

const inputs = [
    'resources/js/app.js',
    ...fg.sync([
        'resources/css/**/*.css',
        'resources/assets/scss/**/*.scss',
        'resources/assets/images/**/*.{png,jpg,jpeg,svg,webp,avif,gif,mp4,webm}',
        'resources/assets/svg/**/*.svg',
    ], { onlyFiles: true }),
];

export default defineConfig({
    plugins: [
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
