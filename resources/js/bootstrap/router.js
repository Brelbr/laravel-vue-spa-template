import Vue from 'vue'
import VueRouter from 'vue-router'
import {routes} from '../core/routes'

Vue.use(VueRouter)


const router = new VueRouter({
    routes,
    mode: 'history',
    base: process.env.MIX_SITE_SUB_URL + '/' + process.env.MIX_ADMIN_PANEL_PREFIX,
    scrollBehavior(to, from, savedPosition) {
        return new Promise((resolve) => {
            if (to.hash) {
                resolve({ selector: to.hash })
            } else if (savedPosition) {
                resolve(savedPosition)
            } else {
                resolve({x: 0, y: 0})
            }
        })
    }
})

Vue.router = router

export default router
