/* eslint-env node */
require('@rushstack/eslint-patch/modern-module-resolution');

module.exports = {
  root: true,

  env: {
    browser: true,
    es6: true,
    node: true
  },

  parser: 'vue-eslint-parser',

  parserOptions: {
    parser: '@typescript-eslint/parser',
    ecmaVersion: 'latest'
  },

  ignorePatterns: ['types/**/*'],

  rules: {
    'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'comma-dangle': ['error', 'never'],
    'vue/no-multiple-template-root': 'off',
    'vue/multi-word-component-names': 'off',
    'vue/script-setup-uses-vars': 'error',
    'vue/require-default-prop': 'off',
    'vue/no-deprecated-slot-attribute': 'off',
    'vue/no-v-html': 'off',
    'vue/require-explicit-emits': 'error',
    'vue/prop-name-casing': 'off', // TODO Should be removed when legacy API snake_case is fixed HPNL-4357
    'vue/match-component-import-name': 'error',
    'vue/component-definition-name-casing': ['error', 'PascalCase'],
    'vue/max-attributes-per-line': [
      'error',
      {
        singleline: {
          max: 7
        },
        multiline: {
          max: 1
        }
      }
    ],
    'vue/html-self-closing': [
      'error',
      {
        html: { normal: 'always', void: 'always', component: 'always' }
      }
    ],
    'vue/component-name-in-template-casing': [
      'error',
      'PascalCase',
      {
        ignores: ['/^hp-/'],
        registeredComponentsOnly: true,
        globals: []
      }
    ],
    'vue/html-indent': 'off',
    'vue/singleline-html-element-content-newline': 'off',
    'arrow-parens': ['error', 'always'],
    'no-nested-ternary': 'error',
    'vue/attribute-hyphenation': [
      'off',
      'error',
      'never',
      {
        ignore: ['custom-prop']
      }
    ],
    'no-multiple-empty-lines': ['error', { max: 1, maxEOF: 1 }],
    'no-trailing-spaces': ['error'],
    'quote-props': ['error', 'as-needed'],
    semi: ['error', 'always'],
    'prefer-const': 'error',
    'no-const-assign': 'error',
    'no-array-constructor': 'error',
    'no-new-object': 'error',
    'newline-before-return': ['error'],
    'import/order': [
      'error',
      {
        'newlines-between': 'never',
        groups: ['builtin', 'external', 'internal', 'sibling', 'parent'],
        alphabetize: {
          order: 'asc'
        }
      }
    ],
    'import/extensions': [
      'error',
      'ignorePackages',
      {
        '': 'never',
        js: 'never',
        ts: 'never',
        vue: 'ignorePackages'
      }
    ],
    '@typescript-eslint/no-unused-vars': 'off',
    'no-unused-vars': 'off',
    'unused-imports/no-unused-vars': [
      'error',
      {
        vars: 'all',
        varsIgnorePattern: '^_',
        args: 'after-used',
        argsIgnorePattern: '^_'
      }
    ],
    'func-style': 'error',
    'wrap-iife': 'error',
    'no-loop-func': 'error',
    'prefer-rest-params': 'error',
    'no-new-func': 'error',
    'no-duplicate-imports': 'error',
    'prefer-promise-reject-errors': 'error',
    'no-param-reassign': [
      'error',
      {
        props: false
      }
    ],
    'prefer-spread': 'error',
    'prettier/prettier': [
      'error',
      {
        semi: true,
        tabWidth: 2,
        singleQuote: true,
        printWidth: 120,
        trailingComma: 'none',
        htmlWhitespaceSensitivity: 'ignore'
      }
    ],
    'vue/order-in-components': 'off',
    'vue/match-component-file-name': [
      'error',
      {
        extensions: ['vue'],
        shouldMatchCase: false
      }
    ],
    'arrow-spacing': 'error',
    'prefer-arrow-callback': 'error',
    'arrow-body-style': ['error', 'as-needed']
  },

  extends: ['@vue/typescript', 'plugin:vue/base', 'plugin:vue/vue3-recommended', 'prettier', '@vue/prettier'],

  plugins: ['@typescript-eslint', 'import', 'vue', 'prettier', 'modules-newlines', 'unused-imports']
};
