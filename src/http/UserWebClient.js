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
   * @param {string} username Chave primaria para identificar o usuario.
   * @param {string} email Email do usuario, deve coincidir com o banco de dados.
   * @returns {Promise<void>}
   */
  async resetSenha(username, email) {
    const params = {
      usuario: username,
      email: email,
    };
    await this.http.delete('/usuario/passwd', {params});
  }
}
