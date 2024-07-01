import {createRouter, createWebHistory} from 'vue-router/dist/vue-router'
import EnvelopeList from "../pages/envelope/EnvelopeList.vue";
import CategoryList from "../pages/category/CategoryList.vue";
import MovementList from "../pages/movement/MovementList.vue";

export default () => createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/budget',
            name: 'home',
            component: () => import('../pages/Home.vue'),
            // children: [
            //     {
            //         path: ':id',
            //         name: 'budget-view',
            //         component: () => import('../pages/Budget.vue'),
            //     },
            // ]

        },
        {
            path: '/budget/:id',
            name: 'budget-view',
            component: () => import('../pages/Budget.vue'),
        },
        {
            path: '/budget/:budgetId/envelopes',
            name: 'budget-envelopes',
            component: EnvelopeList,
        },
        {
            path: '/budget/:budgetId/categories',
            name: 'budget-categories',
            component: CategoryList,
        },
        {
            path: '/budget/:budgetId/movements',
            name: 'budget-movements',
            component: MovementList,
            parent: 'budget-view',
        }
    ],
})
