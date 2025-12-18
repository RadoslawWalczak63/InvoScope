import { PrimeVueResolver } from '@primevue/auto-import-resolver';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import Components from 'unplugin-vue-components/vite';
import { defineConfig, loadEnv } from 'vite';

export default ({ mode }) => {
    process.env = { ...process.env, ...loadEnv(mode, process.cwd()) };

    const isTesting = mode === 'testing';

    const hmrHost = process.env.VITE_HMR_HOST || 'invoscope.localhost';

    return defineConfig({
        server: {
            host: true,
            port: parseInt(process.env.VITE_PORT) || 5173,
            cors: { origin: '*' },
            hmr: isTesting
                ? undefined
                : {
                      host: hmrHost,
                      port: parseInt(process.env.VITE_PORT) || 5173,
                      protocol: 'ws',
                  },
            allowedHosts: 'all',
        },
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.ts'],
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
