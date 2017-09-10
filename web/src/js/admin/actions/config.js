/**
 * @param {string} method
 * @param {string} body
 * @returns {{method: string, body: string, credentials: string, headers: *}}
 */
export function fetchConfig(method, body = null) {
  return {
    method,
    body,
    credentials: 'same-origin',
    headers:     {
      'Accept':       'application/json',
      'Content-Type': 'application/json'
    }
  };
}
