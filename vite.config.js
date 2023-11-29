import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue2';

// ckeditor5
import ckeditor5 from '@ckeditor/vite-plugin-ckeditor5';
import { createRequire } from 'node:module';
const require = createRequire(import.meta.url);

import laravel, { refreshPaths } from 'laravel-vite-plugin';
const path = require("path");

export default defineConfig({

    server: {
        hmr: {
            host: 'localhost'
        },
        usePolling: true
    },


    /*
    ! Use this server conf if u want to serve vite for local network
        server: {
            host: '192.168.1.3', // your local ip
            port: 8001,
            hmr: {
                host: '192.168.1.3'
            },
            usePolling: true
        },
    */

    plugins: [
        laravel({
            input: [
                'resources/js/app.js'
            ],
            refresh: [
                ...refreshPaths
            ],
            buildDirectory: '/build'
        }),
        vue(),
        ckeditor5({ theme: require.resolve('@ckeditor/ckeditor5-theme-lark') })
    ],
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
    },
    resolve: {
        extensions: [
            ".mjs",
            ".js",
            ".ts",
            ".jsx",
            ".tsx",
            ".json",
            ".vue",
            ".scss",
        ],
        alias: {
            '@': path.resolve(__dirname, "./resources/js"),
            'img': path.resolve(__dirname, "./resources/images"),
            'scss': path.resolve(__dirname, "./resources/scss")
        }
    }
});
