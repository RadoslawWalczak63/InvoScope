import { PrimeVueResolver } from '@primevue/auto-import-resolver';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import Components from 'unplugin-vue-components/vite';
import { defineConfig, loadEnv } from 'vite';

export default ({ mode }) => {
    process.env = { ...process.env, ...loadEnv(mode, process.cwd()) };

    return defineConfig({
        server: {
            host: true,
            port: 5173,
            https: false,
            cors: {
                origin: '*',
            },
            hmr: {
                host: process.env.VITE_HMR_HOST,
            },
            allowedHosts: true,
        },
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
            Components({
                resolvers: [PrimeVueResolver()],
            }),
        ],
    });
};
