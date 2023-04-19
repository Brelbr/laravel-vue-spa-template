import Index        from './components/Index'
import NotFound     from './components/NotFound'
import Welcome      from './components/Welcome'
import Home         from './components/Home'
import auth         from '../modules/auth/routes_auth'

// import Login from '../modules/auth/components/Login'

// Load modules routes dynamically.
const requireContext = require.context('../modules', true, /routes\.js$/)
console.log(requireContext.keys())
const modules = requireContext.keys()
    .map(file =>
        [file.replace(/(^.\/)|(\.js$)/g, ''), requireContext(file)]
    )
console.log(...modules)
let moduleRoutes = []

for(let i in modules) {
    moduleRoutes = moduleRoutes.concat(modules[i][1].routes)
}

export const routes = [
    {
        path: '/admin',
        component: Home,
        meta: {auth: true},
        children: [
            ...moduleRoutes,
        ]
    },
    {
        path: '/',
        component: Welcome,
        children: [
            {
                path: '/',
                component: Index,  // Or Login/Register 
                name: 'index',
                meta: {
                    reload: true,
                },
            },
            ...auth,
            {
                path: '*',
                component: NotFound,
                name: 'not_found'
            }
        ]
    },
]

