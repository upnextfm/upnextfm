/**
 *
 * @param {Array} users
 * @param {String} username
 * @returns {Object}
 */
export function usersFindByUsername(users, username) {
  for (let i = 0; i < users.length; i++) {
    if (users[i].username === username) {
      return users[i];
    }
  }

  return {};
}
