import Vue from 'vue'
import VueRouter from 'vue-router'
import Lecturas from '../views/Lecturas.vue'
import Precios from '../views/Precios.vue'
import Test from '../views/Test.vue'

Vue.use(VueRouter)

const routes = [
    {path: '/', name: 'Home', component: Precios},
    {path: '/test', name: 'Test', component: Test},
    {path: '/lecturas', name: 'Lecturas', component: Lecturas},
    // {path: '*', redirect: '/'}
]

const router = new VueRouter({
    routes
})

export default router
