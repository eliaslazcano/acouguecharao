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

  /**
   * Obtem a listagem de produtos
   * @returns {Promise<any[]>}
   */
  async getProdutos() {
    const {data} = await this.http.get('/produtos');
    return data;
  }

  /**
   * Insere um produto novo.
   * @param {string} nome Nome do produto.
   * @param {string} codigo Codigo de barras numérico.
   * @param {number} preco Valor de venda por unidade ou quilo.
   * @returns {Promise<number>} Obtem o ID do produto.
   */
  async insertProduto(nome, codigo, preco) {
    const formData = new FormData();
    formData.append('nome', nome.trim());
    formData.append('codigo', codigo.trim());
    formData.append('preco', preco.toString());
    const {data} = await this.http.post('/produtos', formData);
    return data;
  }

  /**
   * Atualiza o produto.
   * @param {number} id Chave primaria.
   * @param nome Nome do produto.
   * @param codigo Codigo de barras numérico.
   * @param preco Valor de venda por unidade ou quilo.
   * @returns {Promise<void>}
   */
  async updateProduto(id, nome, codigo, preco) {
    const formData = new FormData();
    formData.append('id', id.toString());
    formData.append('nome', nome.trim());
    formData.append('codigo', codigo.trim());
    formData.append('preco', preco.toString());
    await this.http.post('/produtos', formData);
  }

  /**
   * Apaga um produto.
   * @param {number} id Chave primaria.
   * @returns {Promise<void>}
   */
  async deleteProduto(id) {
    await this.http.delete('/produtos?id=' + id);
  }
}
