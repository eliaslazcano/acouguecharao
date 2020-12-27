import router from '@/router';
import JwtHelper from '../../helpers/JwtHelper';
import SessionWebClient from "@/http/SessionWebClient";

export default {
  namespaced: true,
  state: () => ({
    token: null,
    intervalId: null,
  }),
  mutations: {
    setToken(state, token) {
      state.token = token;
    },
    setIntervalId(state, intervalId) {
      state.intervalId = intervalId;
    },
  },
  actions: {
    /**
     * Destroi a sessão, retornando para a tela de Login.
     * @param commit
     * @returns {Promise<void>}
     */
    async destroy({ commit }) {
      commit('setToken', null);
      await router.push({name: 'Login'});
    },

    /**
     * Valida a sessão atual de login.
     * @param state
     * @param commit
     * @param {boolean} ocultarLoader
     * @returns {Promise<boolean|null>} True = Logado e validado, False = Login inválido, Null = Não está logado.
     */
    async validateSession({state, commit}, ocultarLoader) {
      let success = null;
      if (state.token) {
        if (!ocultarLoader) commit('setLoader', true, {root: true});
        const sessionWebClient = new SessionWebClient();
        try {
          await sessionWebClient.validateSession();
          success = true;
        } catch (e) {
          success = false;
        } finally {
          if (!ocultarLoader) commit('setLoader', false, {root: true});
        }
      }
      if (state.intervalId !== null && success === false) {
        window.clearInterval(state.intervalId);
        commit('setIntervalId', null);
      }
      return success;
    },

    /**
     * Fica validando periodicamente (a cada 60s) a sessão de login. Quando a validação der negativo, esta função se encerra.
     * @param dispatch
     * @param commit
     * @returns {Promise<void>}
     */
    async useIntervalValidator({dispatch, commit}) {
      const intervalId = window.setInterval(() => dispatch('validateSession', true), 60000);
      commit('setIntervalId', intervalId);
    },
  },
  getters: {
    logged(state) {
      return !!state.token;
    },
    payload(state) {
      return state.token ? JwtHelper.getPayload(state.token) : null;
    },
    checkPermission: (state, getters) => (permissionId) => {
      if (!getters.payload || !getters.payload.permissions) return false;
      return !!getters.payload.permissions.find(p => p.id === permissionId && p.status === true);
    }
  },
}