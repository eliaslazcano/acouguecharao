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
    const {data} = await this.http.get('/vendas');
    return data;
  }

  /**
   * Obtem os dados de uma venda.
   * @param {number|string} id Chave primaria da venda.
   * @returns {Promise<any>}
   */
  async getVenda(id) {
    const {data} = await this.http.get('/vendas?id=' + id);
    return data;
  }

  /**
   * Registra uma venda
   * @param {any[]} produtos
   * @param {number} desconto
   * @returns {Promise<*>}
   */
  async insertVenda(produtos, desconto) {
    const venda = {produtos, desconto};
    const {data} = await this.http.post('/vendas', venda);
    return data;
  }

  async getProdutos() {
    const {data} = await this.http.get('/produtos');
    return data;
  }
}
