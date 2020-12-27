import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import vuetify from './plugins/vuetify';
import '@babel/polyfill'
import 'roboto-fontface/css/roboto/roboto-fontface.css'
import '@mdi/font/css/materialdesignicons.css'
import { config } from './config'
import { Alert, Snackbar } from './models'
import './plugins/axios'
import moment from 'moment'

window.document.title = config.pageTitle;
moment.locale('pt-br');

Vue.config.productionTip = false;

/**
 * Exibe um alerta
 * @param {string} text
 * @param {'success'|'info'|'warning'|'error'} type
 * @param {'top'|'right'|'bottom'|'left'} border
 * @param {boolean} coloredBorder
 * @param {boolean} dense
 * @param {boolean} dismissible
 */
Vue.prototype.$alert = ({text, type = 'info', border = undefined, coloredBorder = false, dense = false, dismissible = false}) => {
  store.commit('alert/show', new Alert({text, type, border, coloredBorder, dense, dismissible}));
};

/**
 * Exibe um loader
 * @param {boolean} active
 */
Vue.prototype.$loading = (active) => {
  if (active) store.commit('loader/show');
  else store.commit('loader/hide');
};

/**
 * Exibe o snackbar global da aplicação
 * @param {string} text
 * @param {string} color
 * @param {boolean} top
 * @param {boolean} bottom
 * @param {boolean} left
 * @param {boolean} right
 * @param {number} timeout
 * @param {boolean} vertical
 * @param {boolean} multiLine
 * @param {boolean} absolute
 */
Vue.prototype.$snackbar = ({text, color = 'primary', top = false, bottom = false, left = false, right = false, timeout = 5000, vertical = false, multiLine = false, absolute = false}) => {
  store.commit('snackbar/show', new Snackbar({
    text,
    color,
    top,
    bottom,
    left,
    right,
    timeout,
    vertical,
    multiLine,
    absolute,
  }));
};

store.dispatch('initializeApp');

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
