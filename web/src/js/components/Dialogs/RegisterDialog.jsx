import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { registerSubmit, registerReset } from 'actions/registerActions';
import FormDialog from 'components/Dialogs/FormDialog';
import InputGroup from 'components/Forms/InputGroup';

const FORM_STATE = {
  username:       '',
  password:       '',
  password2:      '',
  email:          '',
  tos:            false,
  tosError:       '',
  usernameError:  '',
  emailError:     '',
  passwordError:  '',
  password2Error: ''
};

class RegisterDialog extends React.PureComponent {
  static propTypes = {
    isOpen:   PropTypes.bool,
    onClose:  PropTypes.func,
    dispatch: PropTypes.func
  };

  static defaultProps = {
    isOpen:   false,
    onClose:  () => {},
    dispatch: () => {}
  };

  constructor(props) {
    super(props);
    this.state = FORM_STATE;
  }

  componentDidUpdate(prevProps) {
    Materialize.updateTextFields();
    if (prevProps.register.isSubmitting !== this.props.register.isSubmitting && this.props.register.isRegistered) {
      this.props.onClose();
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
      this.setState({ usernameError: 'Required value' });
      return;
    }
    this.setState({ usernameError: '' });
    if (!email) {
      this.setState({ emailError: 'Required value' });
      return;
    }
    this.setState({ emailError: '' });
    if (!password) {
      this.setState({ passwordError: 'Required value' });
      return;
    }
    this.setState({ passwordError: '' });
    if (!password2) {
      this.setState({ password2Error: 'Required value' });
      return;
    }
    this.setState({ password2Error: '' });
    if (password !== password2) {
      this.setState({ passwordError: 'Required value' });
      return;
    }

    if (!tos) {
      this.setState({ tosError: 'Required value' });
      return;
    }
    this.setState({ tosError: '' });

    this.props.dispatch(registerSubmit({ username, email, password }));
  };

  handleClose = () => {
    this.props.dispatch(registerReset());
    this.setState(FORM_STATE);
    this.props.onClose();
  };

  render() {
    const { isOpen, register } = this.props;
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
        open={isOpen}
        submitting={register.isSubmitting}
        onSubmit={this.handleSubmit}
        onClose={this.handleClose}
      >
        <InputGroup label="Username" error={usernameError}>
          <input
            name="username"
            id="input-username"
            type="text"
            value={username}
            disabled={register.isSubmitting}
            onChange={this.handleChangeInput}
            required
            autoFocus
          />
        </InputGroup>
        <InputGroup label="Email" error={emailError}>
          <input
            name="email"
            id="input-email"
            type="email"
            value={email}
            disabled={register.isSubmitting}
            onChange={this.handleChangeInput}
            required
          />
        </InputGroup>
        <InputGroup label="Password" error={passwordError}>
          <input
            name="password"
            id="input-password"
            type="password"
            value={password}
            disabled={register.isSubmitting}
            onChange={this.handleChangeInput}
            required
          />
        </InputGroup>
        <InputGroup label="Password Verify" error={password2Error}>
          <input
            name="password2"
            id="input-password2"
            type="password"
            value={password2}
            disabled={register.isSubmitting}
            onChange={this.handleChangeInput}
            required
          />
        </InputGroup>
        <p>
          <input
            name="tos"
            id="input-tos"
            type="checkbox"
            checked={tos}
            disabled={register.isSubmitting}
            onChange={this.handleChangeInput}
            required
          />
          <label htmlFor="input-tos">
            Agree to Terms of Service
          </label>
        </p>
      </FormDialog>
    );
  }
}

function mapStateToProps(state) {
  return {
    register: Object.assign({}, state.register)
  };
}

export default connect(mapStateToProps)(RegisterDialog);
