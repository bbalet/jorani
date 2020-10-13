/**
 * CryptoTools module handles asymetric encryption using RSA-OAEP algorithm
 * The options are suitable to work with phpseclib
 */
const crypto = window.crypto || window.msCrypto;
const encryptAlgorithm = {
  name: "RSA-OAEP",
  hash: {
    name: "SHA-1"
  }
};

export default class CryptoTools {

  /**
   * Wrapper around btoa from a Uint8Array
   * @param {*} arrayBuffer 
   */
  arrayBufferToBase64String(arrayBuffer) {
    var byteArray = new Uint8Array(arrayBuffer)
    var byteString = '';
    for (let i=0; i<byteArray.byteLength; i++) {
      byteString += String.fromCharCode(byteArray[i]);
    }
    return btoa(byteString);
  }

  /**
   * Wrapper around atob to Uint8Array
   * @param {*} b64str 
   */
  base64StringToArrayBuffer(b64str) {
    var byteStr = atob(b64str);
    var bytes = new Uint8Array(byteStr.length);
    for (let i = 0; i < byteStr.length; i++) {
      bytes[i] = byteStr.charCodeAt(i);
    }
    return bytes.buffer;
  }

  /**
   * Encode an ASCII string into a Uint8Array
   * @param {*} str 
   */
  textToArrayBuffer(str) {
    var buf = unescape(encodeURIComponent(str)); // 2 bytes for each char
    var bufView = new Uint8Array(buf.length);
    for (let i=0; i < buf.length; i++) {
      bufView[i] = buf.charCodeAt(i);
    }
    return bufView;
  }

  /**
   * Convert a PEM string into an array buffer
   * @param {*} pem PEM string
   */
  convertPemToBinary(pem) {
    var lines = pem.split('\n');
    var encoded = '';
    for(let i = 0;i < lines.length;i++){
      if (lines[i].trim().length > 0 &&
          lines[i].indexOf('-BEGIN RSA PRIVATE KEY-') < 0 && 
          lines[i].indexOf('-BEGIN RSA PUBLIC KEY-') < 0 &&
          lines[i].indexOf('-BEGIN PUBLIC KEY-') < 0 &&
          lines[i].indexOf('-END PUBLIC KEY-') < 0 &&
          lines[i].indexOf('-END RSA PRIVATE KEY-') < 0 &&
          lines[i].indexOf('-END RSA PUBLIC KEY-') < 0) {
        encoded += lines[i].trim();
      }
    }
    return this.base64StringToArrayBuffer(encoded);
  }

  /**
   * Import a key stored in a PEM format
   * @param {*} pemKey 
   */
  importPublicKey(pemKey) {
    return new Promise((resolve) => {
      var importer = crypto.subtle.importKey("spki", this.convertPemToBinary(pemKey), encryptAlgorithm, false, ["encrypt"]);
      importer.then((key) => { 
        resolve(key);
      });
    });
  }

  /**
   * Encrypt a message using a public key
   * @param {*} pubkey Public RSA key
   * @param {*} message Text to be encrypted
   */
  encrypt(pubkey, message) {
    return new Promise((resolve) => {
      this.importPublicKey(pubkey).then((key) => {
        crypto.subtle.encrypt(encryptAlgorithm, key, this.textToArrayBuffer(message)).then((cipheredData) => {
          let cipheredValue = this.arrayBufferToBase64String(cipheredData);
          resolve(cipheredValue);
        });
      });
    });
  }

}
