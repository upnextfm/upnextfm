import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { registerToggleDialog, registerReset } from 'actions/registerActions';
import Dialog, { DialogActions, DialogContent, DialogContentText } from 'material-ui/Dialog';
import { FormControlLabel } from 'material-ui/Form';
import { LinearProgress } from 'material-ui/Progress';
import Button from 'material-ui/Button';
import TextField from 'material-ui/TextField';
import Checkbox from 'material-ui/Checkbox';

const FORM_STATE = {
  username:       '',
  password:       '',
  password2:      '',
  email:          '',
  tos:            false,
  tosError:       false,
  usernameError:  false,
  emailError:     false,
  passwordError:  false,
  password2Error: false
};

class RegisterDialog extends Component {
  static propTypes = {
    dispatch: PropTypes.func
  };

  static defaultProps = {
    dispatch: () => {}
  };

  constructor(props) {
    super(props);
    this.state = FORM_STATE;
  }

  componentDidUpdate(prevProps) {
    if (prevProps.isSubmitting !== this.props.isSubmitting && this.props.isRegistered) {
      this.props.dispatch(registerToggleDialog());
    }
  }

  handleChangeInput = (e) => {
    const type = e.target.getAttribute('type');
    this.setState({
      [e.target.name]: (type === 'checkbox') ? e.target.checked : e.target.value
    });
  };

  handleSubmit = () => {
    const username  = this.state.username.trim();
    const email     = this.state.email.trim();
    const password  = this.state.password.trim();
    const password2 = this.state.password2.trim();
    const tos       = this.state.tos;

    if (!username) {
      this.setState({ usernameError: true });
      return;
    }
    this.setState({ usernameError: false });
    if (!email) {
      this.setState({ emailError: true });
      return;
    }
    this.setState({ emailError: false });
    if (!password) {
      this.setState({ passwordError: true });
      return;
    }
    this.setState({ passwordError: false });
    if (!password2) {
      this.setState({ password2Error: true });
      return;
    }
    this.setState({ password2Error: false });
    if (!tos) {
      this.setState({ tosError: true });
      return;
    }
    this.setState({ tosError: false });

    //this.props.dispatch(authLogin({ username, password }));
  };

  handleRequestClose = () => {
    this.props.dispatch(registerReset());
    this.setState(FORM_STATE);
  };

  render() {
    const { isDialogOpen, isSubmitting, error } = this.props;
    const {
      username,
      password,
      password2,
      email,
      tos,
      tosError,
      usernameError,
      emailError,
      passwordError,
      password2Error
    } = this.state;

    return (
      <Dialog open={isDialogOpen} onRequestClose={this.handleRequestClose}>
        <DialogContent>
          {error && (
            <DialogContentText>
              {error}
            </DialogContentText>
          )}
          <TextField
            label="Username"
            name="username"
            value={username}
            onChange={this.handleChangeInput}
            error={usernameError}
            fullWidth
            autoFocus
          />
          <TextField
            label="Email"
            name="email"
            value={email}
            onChange={this.handleChangeInput}
            error={emailError}
            fullWidth
          />
          <TextField
            label="Password"
            name="password"
            type="password"
            value={password}
            onChange={this.handleChangeInput}
            error={passwordError}
            fullWidth
          />
          <TextField
            label="Password Verify"
            name="password2"
            type="password"
            value={password2}
            onChange={this.handleChangeInput}
            error={password2Error}
            fullWidth
          />
          <FormControlLabel
            label="Agree to Terms of Service"
            control={
              <Checkbox
                name="tos"
                checked={tos}
                error={tosError}
                onChange={this.handleChangeInput}
              />
            }
          />
          <div style={{ marginTop: 30 }}>
            {isSubmitting && (
              <LinearProgress />
            )}
          </div>
        </DialogContent>
        <DialogActions>
          <Button onClick={this.handleRequestClose}>
            Cancel
          </Button>
          <Button onClick={this.handleSubmit}>
            Register
          </Button>
        </DialogActions>
      </Dialog>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.register);
}

export default connect(mapStateToProps)(RegisterDialog);
