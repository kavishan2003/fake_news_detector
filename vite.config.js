import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    // server: {
    //     host: true, // or '0.0.0.0'
    //     // port: 5173,
    //     // strictPort: true,
    //     // hmr: {
    //     //     protocol: "wss",
    //     //     host: "508-232d.ngrok-free.app",
    //     //     clientPort: 443,
    //     // },
    // },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
