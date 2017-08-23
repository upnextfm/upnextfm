import * as types from 'actions/actionTypes';

gapi.load('client', () => {
  console.info('loaded');
});

export function youtubeLoadClient() {
  return (dispatch) => {
    gapi.load('client', () => {
      dispatch({
        type: types.YOUTUBE_LOADED
      });
    });
  };
}
