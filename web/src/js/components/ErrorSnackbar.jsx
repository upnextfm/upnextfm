import React from 'react';
import PropTypes from 'prop-types';
import Snackbar from 'material-ui/Snackbar';
import IconButton from 'material-ui/IconButton';
import CloseIcon from 'material-ui-icons/Close';
import ErrorIcon from 'material-ui-icons/Error';

export default class ErrorSnackbar extends React.Component {
  static propTypes = {
    errorMessage: PropTypes.string,
    onClose:      PropTypes.func
  };

  static defaultProps = {
    errorMessage: '',
    onClose:      () => {}
  };

  constructor(props) {
    super(props);
  }

  render() {
    const { errorMessage, onClose } = this.props;

    return (
      <Snackbar
        anchorOrigin={{
          vertical:   'top',
          horizontal: 'center'
        }}
        open={errorMessage !== ''}
        autoHideDuration={6e3}
        className="up-snackbar up-snackbar--error"
        onRequestClose={onClose}
        SnackbarContentProps={{
          'aria-describedby': 'snackbar-message-id'
        }}
        message={(
          <span id="snackbar-message-id" className="up-snackbar__message">
            <ErrorIcon className="up-icon" />
            {errorMessage}
          </span>
        )}
        action={[
          <IconButton
            key="close"
            aria-label="Close"
            color="inherit"
            onClick={onClose}
          >
            <CloseIcon />
          </IconButton>
        ]}
      />
    );
  }
}
