import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { userReset, userLogin } from 'actions/userActions';
import FormControl from 'material-ui/Form/FormControl';
import TextField from 'material-ui/TextField';
import FormDialog from 'components/Dialogs/FormDialog';

class LoginDialog extends Component {
  static propTypes = {
    user:     PropTypes.object,
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
    if (prevProps.user.isSubmitting !== this.props.user.isSubmitting && this.props.user.isAuthenticated) {
      this.props.onClose();
    }
  }

  handleChangeInput = (e) => {
    this.setState({
      [e.target.name]: e.target.value
    });
  };

  handleKeyDown = (e) => {
    if (e.keyCode === 13) {
      this.handleSubmit();
    }
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

    this.props.dispatch(userLogin({ username, password }));
  };

  handleClose = () => {
    this.props.dispatch(userReset());
    this.setState({
      username:      '',
      password:      '',
      usernameError: false,
      passwordError: false
    });
    this.props.onClose();
  };

  render() {
    const { isOpen, user } = this.props;
    const { username, password, usernameError, passwordError } = this.state;

    return (
      <FormDialog
        submitText="Login"
        className="up-dialog up-dialog--login"
        error={user.error}
        open={isOpen}
        submitting={user.isSubmitting}
        onSubmit={this.handleSubmit}
        onClose={this.handleClose}
      >
        <FormControl disabled={user.isSubmitting} fullWidth>
          <TextField
            label="Username"
            name="username"
            value={username}
            error={usernameError}
            onKeyDown={this.handleKeyDown}
            onChange={this.handleChangeInput}
            autoFocus
          />
        </FormControl>
        <FormControl disabled={user.isSubmitting} fullWidth>
          <TextField
            label="Password"
            name="password"
            type="password"
            value={password}
            error={passwordError}
            onKeyDown={this.handleKeyDown}
            onChange={this.handleChangeInput}
          />
        </FormControl>
      </FormDialog>
    );
  }
}

function mapStateToProps(state) {
  return {
    user: Object.assign({}, state.user)
  };
}

export default connect(mapStateToProps)(LoginDialog);
