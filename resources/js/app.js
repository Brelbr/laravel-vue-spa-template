import Vue from 'vue'
import App from './core/App'
import ElementUI from 'element-ui'
import i18n from './bootstrap/i18n'
import axios from 'axios'
import router from './bootstrap/router'
import store from './core/store'
import globalMixin from './includes/mixins/globalMixin'
import auth from './bootstrap/auth'
import './bootstrap/day'

Vue.use(ElementUI, {i18n: (key, value) => i18n.t(key, value)})

Vue.prototype.config = window.config
Vue.config.baseurl = process.env.MIX_SITE_SUB_URL + '/' + process.env.MIX_ADMIN_PANEL_PREFIX

Vue.mixin(globalMixin)

window.Vue = new Vue({
    router,
    store,
    auth,
    i18n,
    render: h => h(App),
    watch: {
        '$route' (to) {            
            if(to.meta.reload==true){
                if(to.meta.reload==true){                
                    axios.get('../..' + process.env.MIX_SITE_SUB_URL + '/' + process.env.MIX_ADMIN_PANEL_PREFIX + '/login').then(response => {              
                        // TODO: This is wrong, need something else      
                        let pos = response.data.search('csrf-token')
                        let token = response.data.slice(pos+21, pos + 61)                  
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = token
                    })                
                }
            }
        }}
}).$mount('#app')
