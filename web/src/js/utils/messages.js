/**
 *
 * @param {{to: string, from: string, date: string, message: string}} message
 * @returns {{to: string, from: string, date: string, message: string}}
 */
export function sanitizeMessage(message) {
  const msg  = Object.assign({}, message);
  msg.date   = new Date(msg.date);
  return msg;
}
