import react from "@vitejs/plugin-react";
import { defineConfig } from "vite";

export default defineConfig({
  plugins: [react()],
  root: "frontend",
  build: { outDir: "../public/build", emptyOutDir: true },
  server: { port: 5173 },
  test: { environment: "jsdom", setupFiles: "./test/setup.ts" },
});
