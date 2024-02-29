import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { viteStaticCopy } from 'vite-plugin-static-copy'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/app.scss',
                'resources/scss/utility.scss',
                'resources/js/app.js',
                'resources/js/app.debug.js',
            ],
            refresh: true
        }),
        viteStaticCopy({
            targets: [{
                src: 'resources/svg/*',
                dest: 'svg'
            },{
                src: 'node_modules/bootstrap-icons/font/fonts/*',
                dest: 'resources/scss/fonts'
            }]
        })
    ],
    resolve: {
        alias: {
            'bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
            'bootstrap-icons': path.resolve(__dirname, 'node_modules/bootstrap-icons'),
            '@webpixels': path.resolve(__dirname, 'node_modules/@webpixels'),
        }
    },
});
