import Vue from 'vue'
import Vuex from 'vuex'
import { alertStore, sessionStore, snackbarStore } from './modules'
import vuexPersist from '@/plugins/vuex-persist'

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    dark: false,
    loader: false,
  },
  mutations: {
    /**
     * Ativa ou desativa o tema escuro
     * @param state
     * @param {boolean} payload
     */
    setDark(state, payload) {
      state.dark = payload;
    },
    /**
     * Exibe ou esconde a tela de carregamento master da aplicação
     * @param state
     * @param {boolean} payload
     */
    setLoader(state, payload) {
      state.loader = payload;
    },
  },
  actions: {
    async initializeApp({dispatch}) {
      const logged = await dispatch('session/validateSession');
      if (logged === true) dispatch('session/useIntervalValidator');
    },
  },
  modules: {
    session: sessionStore,
    snackbar: snackbarStore,
    alert: alertStore,
  },
  plugins: [vuexPersist.plugin],
})
