export default class ImageHelper {
  /**
   * Converte uma image Base64 para Blob.
   * @param {string} dataUri
   * @returns {Blob}
   */
  static dataURItoBlob(dataUri) {
    const dados = dataUri.split(';base64,');
    const mime = dados[0].replace('data:', ''); // image/jpeg
    const byteString = atob(decodeURI(dados[1])); // dados binarios

    let intArray = new Uint8Array(byteString.length);
    for (let i = 0; i < byteString.length; i++) {
      intArray[i] = byteString.charCodeAt(i);
    }

    return new Blob([intArray], {type: mime});
  }

  /**
   * Converte base64 para objeto Image.
   * @param {string} dataUri
   * @returns {Promise<HTMLImageElement>}
   */
  static dataURItoImage_async(dataUri) {
    return new Promise((resolve, reject) => {
      const image = new Image();
      image.onload = () => {
        resolve(image);
      };
      image.onerror = () => {
        reject("O carregamento da imagem falhou");
      };
      image.src = dataUri;
    });
  }

  /**
   * Converte um Blob|File para uma string Base64.
   * @param {File|Blob} file_or_blob
   * @returns {Promise<string>}
   */
  static blobToDataURI_async(file_or_blob) {
    const fileReader = new FileReader();
    return new Promise((resolve, reject) => {
      fileReader.onerror = () => {
        fileReader.abort();
        reject(new Error('Falha na leitura do Blob ou File'));
      };
      fileReader.onload = () => {
        resolve(fileReader.result);
      };
      fileReader.readAsDataURL(file_or_blob);
    });
  }

  /**
   * Converte Blob|File para um Canvas
   * @param {File|Blob} file_or_blob
   * @returns {Promise<HTMLCanvasElement>}
   */
  static async blobToCanvas_async(file_or_blob) {
    const base64 = await ImageHelper.blobToDataURI_async(file_or_blob);
    const image = await ImageHelper.dataURItoImage_async(base64);
    return ImageHelper.imageToCanvas(image);
  }

  //Lê o Blob/File para gerar uma url temporaria Base64. Deixa de existir ao encerrar a janela. Return String (experimental ES6).
  // static blobToDataURI(file_or_blob) {
  //   return URL.createObjectURL(file_or_blob);
  // }

  /**
   * Converte objeto Image para Canvas.
   * @param {HTMLImageElement} image_element
   * @returns {HTMLCanvasElement}
   */
  static imageToCanvas(image_element) {
    const canvas = document.createElement("canvas");
    canvas.width = image_element.width;
    canvas.height = image_element.height;
    canvas.getContext("2d").drawImage(image_element,0,0);
    return canvas;
  }

  /**
   * Faz um crop na imagem, cortando pixels transparentes que são desnecessários.
   * @param {HTMLCanvasElement} canvas
   * @returns {HTMLCanvasElement}
   */
  static trimTransparentBackground(canvas) {
    let ctx = canvas.getContext('2d'),
      copy = document.createElement('canvas').getContext('2d'),
      pixels = ctx.getImageData(0, 0, canvas.width, canvas.height),
      l = pixels.data.length,
      i,
      bound = {
        top: null,
        left: null,
        right: null,
        bottom: null
      },
      x, y;

    for (i = 0; i < l; i += 4) {
      if (pixels.data[i+3] !== 0) {
        x = (i / 4) % canvas.width;
        y = ~~((i / 4) / canvas.width);

        if (bound.top === null) {
          bound.top = y;
        }

        if (bound.left === null) {
          bound.left = x;
        } else if (x < bound.left) {
          bound.left = x;
        }

        if (bound.right === null) {
          bound.right = x;
        } else if (bound.right < x) {
          bound.right = x;
        }

        if (bound.bottom === null) {
          bound.bottom = y;
        } else if (bound.bottom < y) {
          bound.bottom = y;
        }
      }
    }

    let trimHeight = bound.bottom - bound.top,
      trimWidth = bound.right - bound.left,
      trimmed = ctx.getImageData(bound.left, bound.top, trimWidth, trimHeight);

    copy.canvas.width = trimWidth;
    copy.canvas.height = trimHeight;
    copy.putImageData(trimmed, 0, 0);

    return copy.canvas;
  }

  /**
   * Substitui as cores que estiverem acima do nível RGB tolerado.
   * @param {HTMLCanvasElement} canvas
   * @param {Array<Number>} rgb_tolerancia O pixel com cores as três cores RGB mais fortes que o informado serão substituidos.
   * @param {Array<Number>} novaCor_rgba Nova cor RGBA que ficará no lugar.
   * @returns {HTMLCanvasElement}
   */
  static removerCorDeFundo(canvas, rgb_tolerancia = [250, 250, 250], novaCor_rgba = [0, 0, 0, 0]) {
    const ctx = canvas.getContext('2d'),
      imgd = ctx.getImageData(0,0, canvas.width, canvas.height),
      pix = imgd.data;

    for (let i = 0, n = pix.length; i < n; i += 4) {
      const r = pix[i], g = pix[i+1], b = pix[i+2];

      if(r >= rgb_tolerancia[0] && g >= rgb_tolerancia[1] && b >= rgb_tolerancia[2]){
        // Muda para a nova cor.
        pix[i] = novaCor_rgba[0];
        pix[i+1] = novaCor_rgba[1];
        pix[i+2] = novaCor_rgba[2];
        pix[i+3] = novaCor_rgba[3];
      }
    }
    ctx.putImageData(imgd, 0, 0);
    return canvas;
  }

  /**
   * Remove pixels com niveis de transparencia, substituindo pela cor RGB.
   * @param {HTMLCanvasElement} canvas
   * @param {Array<number>} rgb
   * @returns {HTMLCanvasElement}
   */
  static removeAlphaColor(canvas, rgb = [255, 255, 255]) {
    const ctx = canvas.getContext('2d'), imgd = ctx.getImageData(0, 0, canvas.width, canvas.height), pix = imgd.data;
    for (let i = 0, n = pix.length; i < n; i += 4) {
      if (pix[i+3] < 255) { //255 = totalmente visivel. 0 = nada visivel.
        pix[i] = rgb[0];
        pix[i+1] = rgb[1];
        pix[i+2] = rgb[2];
        pix[i+3] = 255;
      }
    }
    ctx.putImageData(imgd, 0, 0);
    return canvas;
  }

  /**
   * Converte um Canvas para base64
   * @param {HTMLCanvasElement} canvas Imagem em canvas.
   * @param {'image/jpeg'|'image/png'} format Formato final do arquivo.
   * @param {number} quality Apenas para JPEG. Valor entre 0 e 1.
   * @returns {string}
   */
  static canvasToDataURI(canvas, format = 'image/png', quality = 0.92) {
    return canvas.toDataURL(format, quality);
  }

  /**
   * Converte uma imagem base64 para outra com o formato desejado
   * @param {string} base64 Imagem atual em base64
   * @param {'image/jpeg'|'image/png'} format Formato desejado.
   * @param {number} quality Apenas para JPEG. Valor entre 0 e 1.
   * @returns {Promise<string>}
   */
  static async dataURIconvert_async(base64, format = 'image/png', quality = 0.92) {
    const image = await ImageHelper.dataURItoImage_async(base64);
    let canvas = ImageHelper.imageToCanvas(image);
    if (format !== 'image/png') canvas = ImageHelper.removeAlphaColor(canvas);
    return ImageHelper.canvasToDataURI(canvas, format, quality);
  }
}