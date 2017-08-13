import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { objectKeyFilter } from 'utils/objects';
import { authToggleLoginDialog, authReset, authLogin } from 'actions/authActions';
import Dialog, { DialogActions, DialogContent, DialogContentText } from 'material-ui/Dialog';
import { CircularProgress } from 'material-ui/Progress';
import Slide from 'material-ui/transitions/Slide';
import FormControl from 'material-ui/Form/FormControl';
import TextField from 'material-ui/TextField';
import Button from 'material-ui/Button';

export default class FormDialog extends Component {
  static propTypes = {
    open:         PropTypes.bool,
    isSubmitting: PropTypes.bool,
    error:        PropTypes.node,
    submitText:   PropTypes.string,
    onClose:      PropTypes.func,
    onSubmit:     PropTypes.func
  };

  static defaultProps = {
    error:      null,
    open:       false,
    submitText: 'Submit',
    onClose:    () => {},
    onSubmit:   () => {}
  };

  render() {
    const { open, submitting, submitText, error, children, ...props } = this.props;

    return (
      <Dialog
        open={open}
        transition={Slide}
        onRequestClose={this.props.onClose}
        {...objectKeyFilter(props, FormDialog.propTypes)}
      >
        <DialogContent>
          {error && (
            <DialogContentText className="up-error">
              {error}
            </DialogContentText>
          )}
          {children}
        </DialogContent>
        <DialogActions>
          <div style={{ maxHeight: 12, marginTop: -12 }}>
            {submitting && (
              <CircularProgress size={22} />
            )}
          </div>
          <Button onClick={this.props.onClose} disabled={submitting}>
            Cancel
          </Button>
          <Button onClick={this.props.onSubmit} disabled={submitting}>
            {submitText}
          </Button>
        </DialogActions>
      </Dialog>
    );
  }
}
