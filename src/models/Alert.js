export default class Alert {
  /**
   *
   * @param {string} text
   * @param {'success'|'info'|'warning'|'error'} type
   * @param {'top'|'right'|'bottom'|'left'} border
   * @param {boolean} coloredBorder
   * @param {boolean} dense
   * @param {boolean} dismissible
   */
  constructor({
      text,
      type = 'info',
      border = undefined,
      coloredBorder = false,
      dense = false,
      dismissible = false,
    } = {}) {
    this.text = text;
    this.type = type;
    this.border = border;
    this.coloredBorder = coloredBorder;
    this.dense = dense;
    this.dismissible = dismissible;
  }
}
