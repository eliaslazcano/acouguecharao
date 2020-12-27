import { httpSnackbar, httpSilent } from '@/plugins/axios'

export default class SessionWebClient {
  http = httpSnackbar;

  /**
   * Construtor
   * @param {boolean} silent
   */
  constructor(silent = false) {
    if (silent) this.http = httpSilent;
  }

  /**
   * Cria uma nova sessão de login, retornando a string JSON WEB TOKEN
   * @param {string} username - Nome de usuário.
   * @param {string} password - Senha.
   * @returns {Promise<string>} - JSON WEB TOKEN.
   */
  async createSession(username, password) {
    const formData = new FormData();
    formData.append('username', username);
    formData.append('password', password);
    const { data } = await this.http.post('/login', formData);
    return data;
  }

  /**
   * Valida a sessão atual do usuário
   * @returns {Promise<string|void>}
   */
  async validateSession(onlyLast = true, newToken = false) {
    const formData = new FormData();
    if (!onlyLast) formData.append('all', 'S');
    if (newToken) formData.append('newtoken', 'S');
    const { data } = await this.http.post('/session', formData);
    return data;
  }
}