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

/**
 *
 * @param {string} activeChat
 * @param {Array} roomMessages
 * @param {*} conversations
 * @returns {Array}
 */
export function findActiveChatMessages(activeChat, roomMessages, conversations) {
  if (activeChat === 'room') {
    return roomMessages;
  }
  if (conversations[activeChat] === undefined) {
    return [];
  }
  return conversations[activeChat].messages;
}

/**
 *
 * @param {*} conversations
 * @param {string} fromUsername
 * @returns {number}
 */
export function getNumNewMessages(conversations, fromUsername) {
  if (conversations[fromUsername] === undefined) {
    return 0;
  }
  return conversations[fromUsername].numNewMessages;
}
