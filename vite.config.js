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

            includeAssets: [
                'favicon.ico',
                'apple-touch-icon.png',
                'masked-icon.svg',
            ],

            manifest: {
                name: 'Sistema Administrativo',
                short_name: 'Admin',
                description: 'Aplicación administrativa interna',
                theme_color: '#0f172a',
                background_color: '#ffffff',
                display: 'standalone',
                orientation: 'portrait',
                start_url: '/',
                scope: '/',
                icons: [
                    {
                        src: '/pwa-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: '/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                    {
                        src: '/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                ],
            },

            workbox: {
                navigateFallback: '/',
                globPatterns: ['**/*.{js,css,html,ico,png,svg,webp}'],
            },
        }),
    ],
});