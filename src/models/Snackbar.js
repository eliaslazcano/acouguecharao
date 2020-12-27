export default class Snackbar {

  /**
   * Construtor
   * @param {string} text - Texto do snackbar
   * @param {string} color - Cor do snackbar
   * @param {boolean} top - Alocar no canto superior
   * @param {boolean} bottom - Alocar no canto inferior
   * @param {boolean} left - Alocar na esquerda
   * @param {boolean} right - Alocar na direita
   * @param {number} timeout - Tempo em milisegundos
   * @param {boolean} vertical - Empilha o conteúdo do Snackbar sobre o botão.
   * @param {boolean} multiLine - Dá ao snackbar uma altura mínima maior [mobile]
   * @param {boolean} absolute - Aplica position absolute ao componente
   */
  constructor({
                text = '', color = 'primary', top = false, bottom = false, left = false, right = false, timeout = 5000, vertical = false, multiLine = false, absolute = false
              } = {}) {
    this.text = text;
    this.color = color;
    this.top = top;
    this.bottom = bottom;
    this.left = left;
    this.right = right;
    this.timeout = timeout;
    this.vertical = vertical;
    this.multiLine = multiLine;
    this.absolute = absolute;
  }
}
