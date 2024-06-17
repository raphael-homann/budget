import { createRouter, createWebHistory } from 'vue-router/dist/vue-router'

export default () => createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/budget/',
            name: 'home',
            component: () => import('../pages/Home.vue'),
        },
        {
            path: '/budget/:id',
            name: 'budget-view',
            component: () => import('../pages/Budget.vue'),
        },
        {
            path: '/budget/:budgetId/envelopes',
            name: 'budget-envelopes',
            component: () => import('../pages/envelope/EnvelopeList.vue'),
        }
    ],
})
