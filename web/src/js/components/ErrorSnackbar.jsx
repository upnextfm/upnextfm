import React from 'react';
import PropTypes from 'prop-types';
import Snackbar from 'material-ui/Snackbar';
import IconButton from 'material-ui/IconButton';
import Icon from 'components/Icon';

export default class ErrorSnackbar extends React.PureComponent {
  static propTypes = {
    errorMessage: PropTypes.string,
    duration:     PropTypes.number,
    onClose:      PropTypes.func
  };

  static defaultProps = {
    errorMessage: '',
    duration:     30000,
    onClose:      () => {}
  };

  render() {
    const { errorMessage, duration, onClose } = this.props;

    return (
      <Snackbar
        anchorOrigin={{
          vertical:   'top',
          horizontal: 'center'
        }}
        open={errorMessage !== ''}
        autoHideDuration={duration}
        className="up-snackbar up-snackbar--error"
        onRequestClose={onClose}
        SnackbarContentProps={{
          'aria-describedby': 'snackbar-message-id'
        }}
        message={(
          <span id="snackbar-message-id" className="up-snackbar__message">
            <Icon name="error" className="up-icon" />
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
            <Icon name="close" />
          </IconButton>
        ]}
      />
    );
  }
}
