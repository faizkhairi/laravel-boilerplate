import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import globals from 'globals';

export default [
    {
        ignores: ['public/**', 'vendor/**', 'node_modules/**', 'bootstrap/ssr/**'],
    },
    js.configs.recommended,
    ...pluginVue.configs['flat/essential'],
    {
        files: ['resources/js/**/*.{js,vue}'],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: {
                ...globals.browser,
                // Ziggy's route() helper, injected globally by ZiggyVue.
                route: 'readonly',
            },
        },
    },
    {
        // Inertia resolves pages by file name (Dashboard, Welcome), and the
        // Breeze components mirror HTML elements (Checkbox, Modal).
        files: ['resources/js/**/*.vue'],
        rules: {
            'vue/multi-word-component-names': 'off',
        },
    },
];
