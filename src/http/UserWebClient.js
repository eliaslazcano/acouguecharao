import { httpSnackbar, httpSilent } from '@/plugins/axios'

export default class UserWebClient {
  http = httpSnackbar

  /**
   * Construtor
   * @param {boolean} silent
   */
  constructor(silent = false) {
    if (silent) this.http = httpSilent;
  }

  /**
   * Solicita um reset de senha para um usuario. Ele receberá uma nova senha por email.
   * @param {string} username para identificar o usuario.
   * @returns {Promise<string>} Retorna o email do destinatario.
   */
  async resetSenha(username) {
    const params = {
      username,
    };
    const {data} = await this.http.delete('/usuario/passwd', {params});
    return data;
  }
}
