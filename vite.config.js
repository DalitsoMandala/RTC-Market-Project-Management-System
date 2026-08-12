import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    server: {
        // Forces Vite to use IPv4 instead of IPv6 [::1]
        host: "127.0.0.1",
        cors: {
            // Allows your Laravel app to read the Vite client assets
            origin: "*",
            methods: ["GET", "OPTIONS"],
        },
    },
});
