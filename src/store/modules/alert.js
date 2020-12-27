export default {
  namespaced: true,
  state: () => ({
    alerts: [], //Array<Alert>
    timeout: 5000,
  }),
  mutations: {
    /**
     * Exibe um alerta
     * @param state
     * @param {Alert} alert
     */
    show(state, alert) {
      if (state.alerts.find(a => a.text === alert.text)) return;
      const id = state.alerts.length + 1;
      state.alerts.unshift({id, ...alert});
      setTimeout(() => state.alerts.pop(), state.timeout);
    },
  },
  // actions: {},
  // getters: {},
};
