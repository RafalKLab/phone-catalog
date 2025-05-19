import { createRouter, createWebHistory } from 'vue-router'
import CatalogPage from '@/pages/Catalog/Catalog.vue'

const routes = [
    {
        path: '/',
        name: 'Catalog',
        component: CatalogPage
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
