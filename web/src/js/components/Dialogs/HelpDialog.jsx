import React from 'react';
import PropTypes from 'prop-types';
import Dialog, { DialogActions, DialogContent, DialogTitle, DialogContentText } from 'material-ui/Dialog';
import Slide from 'material-ui/transitions/Slide';
import Button from 'material-ui/Button';

export default class Component extends React.PureComponent {
  static propTypes = {
    isOpen:  PropTypes.bool,
    onClose: PropTypes.func
  };

  static defaultProps = {
    isOpen:  false,
    onClose: () => {}
  };

  render() {
    const { isOpen, onClose } = this.props;

    return (
      <Dialog
        open={isOpen}
        transition={Slide}
        onRequestClose={onClose}
        className="up-dialog"
      >
        <DialogTitle>Help</DialogTitle>
        <DialogContent style={{ minWidth: '380px' }}>
          <h4>Commands</h4>
          <ul>
            <li>/pm [username] [message]</li>
          </ul>
        </DialogContent>
        <DialogActions>
          <Button onClick={onClose}>
            Close
          </Button>
          <Button onClick={() => { window.open('/help'); }}>
            Advanced
          </Button>
        </DialogActions>
      </Dialog>
    );
  }
}
