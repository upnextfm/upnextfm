import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { authToggleDialog, login } from 'actions/authActions';
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
      username: '',
      password: ''
    };
  }

  componentDidUpdate(prevProps) {
    if (prevProps.isSubmitting !== this.props.isSubmitting && this.props.isAuthenticated) {
      this.props.dispatch(authToggleDialog());
      this.setState({ username: '', password: '' });
    }
  }

  handleChangeInput = (e) => {
    this.setState({
      [e.target.name]: e.target.value
    });
  };

  handleSubmit = () => {
    this.props.dispatch(login(this.state));
  };

  handleRequestClose = () => {
    this.props.dispatch(authToggleDialog());
  };

  render() {
    const { isDialogOpen, isSubmitting, errorMessage } = this.props;
    const { username, password } = this.state;

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
            fullWidth
            autoFocus
          />
          <TextField
            label="Password"
            name="password"
            value={password}
            onChange={this.handleChangeInput}
            type="password"
            fullWidth
          />
          <div style={{ marginTop: 30 }}>
            {isSubmitting && (
              <LinearProgress />
            )}
          </div>
        </DialogContent>
        <DialogActions>
          <Button onClick={this.handleRequestClose} color="primary">
            Cancel
          </Button>
          <Button onClick={this.handleSubmit} color="primary">
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
