import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    server: {
        host: true,
        port: 5173,
        cors: true,
        hmr: {
            host: 'localhost',
            port: 5173,
        },
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: 'manifest.json',
        rollupOptions: {
            input: 'resources/js/app.js',
        },
        assetsDir: 'assets',
    },
    define: {
        __VUE_OPTIONS_API__: true,
        __VUE_PROD_DEVTOOLS__: false,
    },
    base: process.env.APP_ENV === 'production' ? '/' : '/',
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            registerType: 'autoUpdate',
            includeAssets: ['favicon.svg', 'robots.txt', 'images/kromerce-business-text.png', 'images/icons/kromerce-business-icon-app-100.png', 'images/icons/kromerce-business-icon-logo-32.png'],
            manifest: {
                name: 'Kromerce - E-commerce Platform',
                short_name: 'Kromerce',
                description: 'E-commerce platform for modern businesses',
                theme_color: '#3b82f6',
                background_color: '#ffffff',
                display: 'standalone',
                orientation: 'portrait-primary',
                scope: '/',
                start_url: '/',
                filename: 'pwa.manifest.json',
                icons: [
                    {
                        src: 'images/icons/kromerce-business-icon-app-100.png',
                        sizes: '100x100',
                        type: 'image/png',
                        purpose: 'any maskable'
                    },
                    {
                        src: 'images/icons/kromerce-business-icon-logo-32.png',
                        sizes: '32x32',
                        type: 'image/png',
                        purpose: 'any'
                    }
                ]
            },
            strategies: 'generateSW',
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,json}'],
                runtimeCaching: [
                    {
                        urlPattern: /^https:\/\/api\./i,
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'api-cache',
                            expiration: {
                                maxEntries: 10,
                                maxAgeSeconds: 60 * 60 * 24 * 365
                            }
                        }
                    },
                    {
                        urlPattern: /\.(?:png|jpg|jpeg|svg|gif)$/,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'images-cache',
                            expiration: {
                                maxEntries: 60,
                                maxAgeSeconds: 60 * 60 * 24 * 30
                            }
                        }
                    }
                ]
            }
        }),
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
            buildDirectory: 'build',
        }),
    ],
});
