import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../components/HomeComponent'
import QiNiu from '../components/QiNiuComponent'

Vue.use(VueRouter)

const routes = [
    { path: '/', component: Home},
    { path: '/qiniu', component: QiNiu},
]

// eslint-disable-next-line no-new
const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
})

export default router
