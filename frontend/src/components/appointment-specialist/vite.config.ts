import { resolve } from "node:path";
import { defineConfig, loadEnv } from "vite";
import terser from "@rollup/plugin-terser";

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), "");
  const apiBase = env.VITE_API_BASE || "";

  return {
    define: {
      __API_BASE__: JSON.stringify(apiBase),
    },
    build: {
      target: "es2022",
      lib: {
        entry: resolve(__dirname, "src/index.ts"),
        formats: ["iife"],
        name: "AppointmentSpecialist",
        fileName: () => "appointment-specialist.js",
        cssFileName: "appointment-specialist",
      },
      minify: false,
      cssMinify: true,
      sourcemap: false,
      emptyOutDir: true,
      rollupOptions: {
        treeshake: { preset: "smallest", moduleSideEffects: false },
        output: {
          inlineDynamicImports: true,
          compact: true,
          plugins: mode === "development" ? [] : [
            terser({
              compress: {
                passes: 3,
                ecma: 2022,
                module: true,
                drop_console: true,
                drop_debugger: true,
                pure_getters: true,
                unsafe: true,
                unsafe_arrows: true,
                unsafe_methods: true,
                unsafe_comps: true,
                unsafe_proto: true,
                toplevel: true,
                booleans_as_integers: true,
              },
              mangle: {
                toplevel: true,
                properties: { regex: /^_/ },
              },
              format: { comments: false },
            }),
          ],
        },
      },
    },
    test: {
      environment: "happy-dom",
      globals: true,
      include: ["tests/**/*.test.ts"],
    },
  };
});