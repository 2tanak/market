import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig(({ mode }) => {
	const env = loadEnv(mode, process.cwd(), '');
	return {
		server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true, 
        cors: {
            origin: env.APP_URL,
            credentials: true,
        },
        hmr: {
           host: env.URL,
		   
        },
    },
	 plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
	 resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
		     '@base_resources': path.resolve(__dirname, 'resources/js'),
		     '@modules': path.resolve(__dirname, 'Modules'),
        },
    },
 }
	
});	



