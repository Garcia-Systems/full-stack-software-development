import react from "@vitejs/plugin-react";
import { defineConfig } from "vitest/config";

export default defineConfig({
  plugins: [react()],
  root: "frontend",
  build: { outDir: "../public/build", emptyOutDir: true },
  server: { port: 5173, proxy: { "/api": "http://localhost:8080" } },
  test: { environment: "jsdom", setupFiles: "./test/setup.ts" },
});
