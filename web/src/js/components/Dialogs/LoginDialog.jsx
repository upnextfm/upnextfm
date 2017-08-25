import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { authReset, authLogin } from 'actions/authActions';
import FormControl from 'material-ui/Form/FormControl';
import TextField from 'material-ui/TextField';
import FormDialog from 'components/Dialogs/FormDialog';

class LoginDialog extends Component {
  static propTypes = {
    auth:     PropTypes.object,
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
    this.state = {
      username:      '',
      password:      '',
      usernameError: false,
      passwordError: false
    };
  }

  componentDidUpdate(prevProps) {
    if (prevProps.auth.isSubmitting !== this.props.auth.isSubmitting && this.props.auth.isAuthenticated) {
      this.props.onClose();
    }
  }

  handleChangeInput = (e) => {
    this.setState({
      [e.target.name]: e.target.value
    });
  };

  handleSubmit = () => {
    const username = this.state.username.trim();
    const password = this.state.password.trim();
    if (!username) {
      this.setState({ usernameError: true });
      return;
    }
    this.setState({ usernameError: false });
    if (!password) {
      this.setState({ passwordError: true });
      return;
    }
    this.setState({ passwordError: false });

    this.props.dispatch(authLogin({ username, password }));
  };

  handleClose = () => {
    this.props.dispatch(authReset());
    this.setState({
      username:      '',
      password:      '',
      usernameError: false,
      passwordError: false
    });
    this.props.onClose();
  };

  render() {
    const { isOpen, auth } = this.props;
    const { username, password, usernameError, passwordError } = this.state;

    return (
      <FormDialog
        submitText="Login"
        error={auth.error}
        open={isOpen}
        submitting={auth.isSubmitting}
        onSubmit={this.handleSubmit}
        onClose={this.handleClose}
      >
        <FormControl disabled={auth.isSubmitting} fullWidth>
          <TextField
            label="Username"
            name="username"
            value={username}
            error={usernameError}
            onChange={this.handleChangeInput}
            autoFocus
          />
        </FormControl>
        <FormControl disabled={auth.isSubmitting} fullWidth>
          <TextField
            label="Password"
            name="password"
            type="password"
            value={password}
            error={passwordError}
            onChange={this.handleChangeInput}
          />
        </FormControl>
      </FormDialog>
    );
  }
}

function mapStateToProps(state) {
  return {
    auth: Object.assign({}, state.auth)
  };
}

export default connect(mapStateToProps)(LoginDialog);
