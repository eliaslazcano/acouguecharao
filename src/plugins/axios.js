import Vue from 'vue'
import axios from 'axios'
import store from '@/store'
import { config } from '@/config'
import { Alert, Snackbar } from '@/models';

/**
 * Cria uma instancia Axios com regras da aplicação.
 * @param {function(string)} errorCallback - Callback com mensagem de erro para feedback.
 * @returns {AxiosInstance}
 */
const createWebClient = (errorCallback) => {
  //Axios Instance
  const axiosClient = axios.create({
    baseURL: config.http.requestBaseUrl,
    timeout: config.http.requestTimeout,
    headers: {'Cache-Control': 'no-cache'},
  });
  //Interceptor Request
  const beforeSend = conf => {
    //Insert Authorization Header
    if (store.state.session.token) conf.headers.Authorization = store.state.session.token;
    return conf;
  };
  //Error Handle
  const onError = error => {
    if (error.response) {
      if (error.response.data) {
        if (error.response.data.mensagem) errorCallback(error.response.data.mensagem);
        else errorCallback('Ocorreu um erro na conexão');
      }
      if (error.response.status === 410) store.dispatch('session/destroy');
    }
    else if (error.request) {
      if (error.code === 'ECONNABORTED') errorCallback('A conexão excedeu o tempo limite, tente novamente.')
      else if (error.request.status === 0) errorCallback('Sem conexão com o servidor, verifique a sua internet e tente novamente mais tarde. [' + error.message + ']');
      else errorCallback('ERRO ' + error.request.status);
    }
    // eslint-disable-next-line no-console
    console.error('HTTP Error: ' + error.message);
    return Promise.reject(error);
  };
  //Apply settings
  axiosClient.interceptors.request.use(beforeSend, error => Promise.reject(error));
  axiosClient.interceptors.response.use(res => res, onError);
  return axiosClient;
};

export const http = createWebClient(errorMessage => store.commit('alert/show', new Alert({text: errorMessage, type: 'error'})));
export const httpSnackbar = createWebClient(errorMessage => store.commit('snackbar/show', new Snackbar({text: errorMessage, color: 'error'})));
export const httpAlert = createWebClient(errorMessage => alert(errorMessage));
export const httpSilent = createWebClient(() => null);

Vue.prototype.$http = http;
Vue.prototype.$httpSnackbar = httpSnackbar;
Vue.prototype.$httpAlert = httpAlert;
Vue.prototype.$httpSilent = httpSilent;

export default http;
