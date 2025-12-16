import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    watch: {
      // Use polling for file watching in Docker (required for Windows)
      usePolling: true,
      interval: 1000,
    },
    hmr: {
      // Enable HMR through Docker
      host: 'localhost',
      port: 5173,
    },
  },
})

