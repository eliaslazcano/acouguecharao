import { httpSnackbar, httpSilent } from '@/plugins/axios'

export default class VendaWebClient {
  http = httpSnackbar

  /**
   * Construtor
   * @param {boolean} silent
   */
  constructor(silent = false) {
    if (silent) this.http = httpSilent;
  }

  /**
   * Obtem a listagem de vendas.
   * @returns {Promise<any[]>}
   */
  async getVendas() {
    const {data} = await this.http.get('/venda');
    return data;
  }
}
