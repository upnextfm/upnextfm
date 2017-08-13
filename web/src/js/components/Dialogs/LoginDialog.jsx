import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { authToggleLoginDialog, authReset, authLogin } from 'actions/authActions';
import FormControl from 'material-ui/Form/FormControl';
import TextField from 'material-ui/TextField';
import FormDialog from 'components/Dialogs/FormDialog';

class LoginDialog extends Component {
  static propTypes = {
    dispatch: PropTypes.func
  };

  static defaultProps = {
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
    if (prevProps.isSubmitting !== this.props.isSubmitting && this.props.isAuthenticated) {
      this.props.dispatch(authToggleLoginDialog());
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
  };

  render() {
    const { isLoginDialogOpen, isSubmitting, error } = this.props;
    const { username, password, usernameError, passwordError } = this.state;

    return (
      <FormDialog
        submitText="Login"
        error={error}
        open={isLoginDialogOpen}
        submitting={isSubmitting}
        onSubmit={this.handleSubmit}
        onClose={this.handleClose}
      >
        <FormControl disabled={isSubmitting} fullWidth>
          <TextField
            label="Username"
            name="username"
            value={username}
            error={usernameError}
            onChange={this.handleChangeInput}
            autoFocus
          />
        </FormControl>
        <FormControl disabled={isSubmitting} fullWidth>
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
  return Object.assign({}, state.auth);
}

export default connect(mapStateToProps)(LoginDialog);
