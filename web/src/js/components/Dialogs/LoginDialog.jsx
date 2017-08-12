import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { authToggleDialog, authReset, login } from 'actions/authActions';
import Dialog, { DialogActions, DialogContent, DialogContentText } from 'material-ui/Dialog';
import { LinearProgress } from 'material-ui/Progress';
import Button from 'material-ui/Button';
import TextField from 'material-ui/TextField';

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
      this.props.dispatch(authToggleDialog());
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

    this.props.dispatch(login({ username, password }));
  };

  handleRequestClose = () => {
    this.props.dispatch(authReset());
    this.setState({
      username:      '',
      password:      '',
      usernameError: false,
      passwordError: false
    });
  };

  render() {
    const { isDialogOpen, isSubmitting, errorMessage } = this.props;
    const { username, password, usernameError, passwordError } = this.state;

    return (
      <Dialog open={isDialogOpen} onRequestClose={this.handleRequestClose}>
        <DialogContent>
          {errorMessage && (
            <DialogContentText>
              {errorMessage}
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
            label="Password"
            name="password"
            type="password"
            value={password}
            onChange={this.handleChangeInput}
            error={passwordError}
            fullWidth
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
            Login
          </Button>
        </DialogActions>
      </Dialog>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.auth);
}

export default connect(mapStateToProps)(LoginDialog);
