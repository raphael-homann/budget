import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';


import {createApp} from 'vue'
// Vuetify
import 'vuetify/styles'
import {createVuetify} from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import {fr} from './snippets/fr'
import {en} from './snippets/en'
import {createI18n,useI18n} from "vue-i18n";
import {createVueI18nAdapter} from "vuetify/locale/adapters/vue-i18n";

import App from './App.vue'
import createRouter from "./plugins/router";

const i18n = createI18n({
    legacy: false, // Vuetify does not support the legacy mode of vue-i18n
    locale: 'fr',
    fallbackLocale: 'en',
    messages: {fr, en},
})

const vuetify = createVuetify({
    theme: {
        // defaultTheme: 'dark'
    },
    locale: {
        adapter: createVueI18nAdapter({i18n, useI18n}),
    },
    components,
    directives
})

createApp(App).use(createRouter()).use(vuetify).mount('#app')
