import { createRouter, createWebHistory } from 'vue-router/dist/vue-router'

export default () => createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'home',
            component: () => import('../pages/Home'),
        },
    ],
})
