import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { layoutToggleRegisterDialog } from 'actions/layoutActions';
import { register, registerReset } from 'actions/registerActions';
import { FormControl, FormControlLabel } from 'material-ui/Form';
import TextField from 'material-ui/TextField';
import Checkbox from 'material-ui/Checkbox';
import FormDialog from 'components/Dialogs/FormDialog';

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
    if (prevProps.register.isSubmitting !== this.props.register.isSubmitting && this.props.register.isRegistered) {
      this.props.dispatch(layoutToggleRegisterDialog());
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
    if (password !== password2) {
      this.setState({ passwordError: true });
      return;
    }

    if (!tos) {
      this.setState({ tosError: true });
      return;
    }
    this.setState({ tosError: false });

    this.props.dispatch(register({ username, email, password }));
  };

  handleClose = () => {
    this.props.dispatch(registerReset());
    this.setState(FORM_STATE);
  };

  render() {
    const { layout, register } = this.props;
    const {
      username,
      password,
      password2,
      email,
      tos,
      usernameError,
      emailError,
      passwordError,
      password2Error
    } = this.state;

    return (
      <FormDialog
        submitText="Register"
        error={register.error}
        open={layout.isRegisterDialogOpen}
        submitting={register.isSubmitting}
        onSubmit={this.handleSubmit}
        onClose={this.handleClose}
      >
        <FormControl disabled={register.isSubmitting} fullWidth>
          <TextField
            label="Username"
            name="username"
            value={username}
            error={usernameError}
            onChange={this.handleChangeInput}
            required
            fullWidth
            autoFocus
          />
        </FormControl>
        <FormControl disabled={register.isSubmitting} fullWidth>
          <TextField
            label="Email"
            name="email"
            type="email"
            value={email}
            error={emailError}
            onChange={this.handleChangeInput}
            required
            fullWidth
          />
        </FormControl>
        <FormControl disabled={register.isSubmitting} fullWidth>
          <TextField
            label="Password"
            name="password"
            type="password"
            value={password}
            error={passwordError}
            onChange={this.handleChangeInput}
            required
            fullWidth
          />
        </FormControl>
        <FormControl disabled={register.isSubmitting} fullWidth>
          <TextField
            label="Password Verify"
            name="password2"
            type="password"
            value={password2}
            error={password2Error}
            onChange={this.handleChangeInput}
            required
            fullWidth
          />
        </FormControl>
        <FormControl disabled={register.isSubmitting} fullWidth>
          <FormControlLabel
            label="Agree to Terms of Service"
            control={
              <Checkbox
                name="tos"
                checked={tos}
                onChange={this.handleChangeInput}
                required
              />
            }
          />
        </FormControl>
      </FormDialog>
    );
  }
}

function mapStateToProps(state) {
  return {
    register: Object.assign({}, state.register),
    layout:   Object.assign({}, state.layout)
  };
}

export default connect(mapStateToProps)(RegisterDialog);
