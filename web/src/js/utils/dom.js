/* eslint-disable no-cond-assign */
export function domOnWindowBlur(cb) {
  let hidden = 'hidden';
  const onChange = () => {
    if (document[hidden] !== undefined) {
      cb(document[hidden] ? 'blur' : 'focus');
    }
  };

  if (hidden in document) {
    document.addEventListener('visibilitychange', onChange);
  } else if ((hidden = 'mozHidden') in document) {
    document.addEventListener('mozvisibilitychange', onChange);
  } else if ((hidden = 'webkitHidden') in document) {
    document.addEventListener('webkitvisibilitychange', onChange);
  } else if ((hidden = 'msHidden') in document) {
    document.addEventListener('msvisibilitychange', onChange);
  }

  if (document[hidden] !== undefined) {
    onChange();
  }
}
