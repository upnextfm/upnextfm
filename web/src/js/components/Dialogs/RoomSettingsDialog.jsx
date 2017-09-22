import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Dialog, { DialogActions, DialogContent, DialogTitle } from 'material-ui/Dialog';
import { roomSaveSettings } from 'actions/roomActions';
import Slide from 'material-ui/transitions/Slide';
import FormControl from 'material-ui/Form/FormControl';
import TextField from 'material-ui/TextField';
import Button from 'components/Button';
import InputGroup from 'components/Forms/InputGroup';

class RoomSettingsDialog extends React.PureComponent {
  static propTypes = {
    settings: PropTypes.object.isRequired,
    isOpen:   PropTypes.bool,
    onClose:  PropTypes.func
  };

  static defaultProps = {
    isOpen:  false,
    onClose: () => {}
  };

  constructor(props) {
    super(props);
    this.state = {
      values: props.settings
    };
  }

  componentDidUpdate(prevProps) {
    if (Materialize.updateTextFields) {
      Materialize.updateTextFields();
    }
    if (prevProps.settings !== this.props.settings) {
      this.setState({ values: this.props.settings }); // eslint-disable-line
    }
  }

  handleChange = (e) => {
    const values = Object.assign({}, this.state.values);
    values[e.target.name] = e.target.value;
    this.setState({ values });
  };

  handleSubmit = () => {
    this.props.dispatch(roomSaveSettings(this.state.values, 'room'));
    this.props.onClose();
  };

  render() {
    const { isOpen, onClose } = this.props;
    const { values } = this.state;

    return (
      <Dialog
        open={isOpen}
        transition={Slide}
        onRequestClose={onClose}
        className="up-dialog--wide up-dialog--auto"
      >
        <DialogTitle>Room Settings</DialogTitle>
        <DialogContent>
          <InputGroup label="Join Message">
            <textarea
              rows="4"
              name="joinMessage"
              id="input-join-message"
              className="materialize-textarea"
              value={values.joinMessage}
              onChange={this.handleChange}
            />
          </InputGroup>
        </DialogContent>
        <DialogActions>
          <Button onClick={onClose}>
            Close
          </Button>
          <Button onClick={this.handleSubmit}>
            Save
          </Button>
        </DialogActions>
      </Dialog>
    );
  }
}

function mapStateToProps(state) {
  return {
    settings: Object.assign({}, state.settings.room)
  };
}

export default connect(mapStateToProps)(RoomSettingsDialog);
