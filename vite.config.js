import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: 'localhost',
        port: 5174,
        https: false,
        hmr: {
            host: 'localhost',
            port: 5174,
        },
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    test: {
        environment: 'jsdom',
        globals: true,
        setupFiles: ['./tests/frontend/setup.js'],
        include: ['tests/frontend/**/*.{test,spec}.{js,ts}'],
        coverage: {
            reporter: ['text', 'json', 'html'],
            include: ['resources/js/**/*'],
            exclude: ['node_modules/', 'tests/'],
        },
    },
});
