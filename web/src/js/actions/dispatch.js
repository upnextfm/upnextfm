import * as pmsActions from 'actions/pmsActions';
import * as usersActions from 'actions/usersActions';
import * as userActions from 'actions/userActions';
import * as roomActions from 'actions/roomActions';
import * as layoutActions from 'actions/layoutActions';
import * as playlistActions from 'actions/playlistActions';
import * as playerActions from 'actions/playerActions';
import * as registerActions from 'actions/registerActions';
import * as settingsActions from 'actions/settingsActions';
import * as searchActions from 'actions/searchActions';

const actions = {
  pms:      pmsActions,
  users:    usersActions,
  user:     userActions,
  room:     roomActions,
  player:   playerActions,
  layout:   layoutActions,
  playlist: playlistActions,
  register: registerActions,
  settings: settingsActions,
  search:   searchActions
};

/**
 * payload = {
 *  dispatch: [
 *    { action: 'room:pong', args: [12345] }
 *  ]
 * }
 *
 * @param {Function} dispatch
 * @param {{dispatch: array}} payload
 */
export const dispatchPayload = (dispatch, payload) => {
  if (payload.dispatch !== undefined) {
    for (let i = 0; i < payload.dispatch.length; i += 1) {
      const toDispatch = payload.dispatch[i];
      const [namespace, func] = toDispatch.action.split(':');

      if (actions[namespace] === undefined) {
        console.error(`Action namespace "${namespace}" is not defined.`);
      } else if (actions[namespace][func] === undefined) {
        console.error(`Action "${namespace}:${func}" is not defined.`);
      } else {
        const action = actions[namespace][func];
        const args   = toDispatch.args !== undefined ? toDispatch.args.slice() : [];
        dispatch(action.apply(action, args));
      }
    }
  }
};
