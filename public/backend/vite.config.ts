import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [svelte()],
  build: {
    outDir: '../../dist/backend',
    emptyOutDir: true,
    lib: {
      entry: 'src/main.ts',
      formats: ['iife'],
      name: 'WebPEasy',
      fileName: 'webp-easy',
    },
    manifest: true,
    minify: false,
    sourcemap: true,
    target: 'esnext',

  },
})
