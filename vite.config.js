import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),

        tailwindcss(),

        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: 'auto',
            outDir: 'public',
            filename: 'sw.js',
            manifestFilename: 'manifest.webmanifest',

            includeAssets: [
                'favicon.ico',
                'apple-touch-icon.png',
                'masked-icon.svg',
            ],

            manifest: {
                name: 'Sistema Abonos NuevaEra',
                short_name: 'Abonos',
                description: 'Sistema interno para pedidos, abonos, facturación y clientes',
                theme_color: '#0f172a',
                background_color: '#ffffff',
                display: 'standalone',
                orientation: 'portrait',
                start_url: '/home',
                scope: '/',
                icons: [
                    {
                        src: '/icon/pwa-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: '/icon/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                    {
                        src: '/icon/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                ],
            },

            workbox: {
                navigateFallback: '/home',
                globPatterns: ['**/*.{js,css,html,ico,png,svg,webp,woff2}'],
            },
        }),
    ],
});