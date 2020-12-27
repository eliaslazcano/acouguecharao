export default class JwtHelper {
  /**
   * Construtor
   * @param {string} token - JsonWebToken
   */
  constructor(token) {
    this.token = token;
  }

  /**
   * Obtem os dados carregados no token
   * @returns {null|any}
   */
  getPayload() {
    if (!this.token) return null;
    const parts = this.token.split('.');
    if (parts.length !== 3) return null;
    return JSON.parse(atob(parts[1]));
  }

  /**
   * Obtem os dados carregados no token
   * @param {string} token
   * @returns {null|any}
   */
  static getPayload(token) {
    const jwt = new JwtHelper(token);
    return jwt.getPayload();
  }
}
