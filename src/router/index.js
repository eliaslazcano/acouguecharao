import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/usuario/:usuario',
    name: 'Usuario',
    component: () => import('../views/Config/Usuario'),
    props: true,
  },
  {
    path: '/usuarios',
    name: 'Usuarios',
    component: () => import('../views/Config/Usuarios'),
  },
  {
    path: '/caixa/vendas',
    name: 'Vendas',
    component: () => import('../views/Caixa/Vendas'),
  },
  {
    path: '/caixa/venda',
    name: 'Venda',
    component: () => import('../views/Caixa/Venda'),
  },
]

const router = new VueRouter({
  routes
})

export default router
