import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import IconButton from 'material-ui/IconButton';
import Send from 'material-ui-icons/Send';
import AttachFile from 'material-ui-icons/AttachFile';
import { roomInputChange, roomSend } from 'actions/roomActions';

class MessageInput extends React.Component {
  static propTypes = {
    dispatch: PropTypes.func
  };

  static defaultProps = {
    dispatch: () => {}
  };

  componentDidMount() {
    setTimeout(() => {
      this.inputRef.focus();
    }, 1000);
  }

  send = () => {
    this.props.dispatch(roomSend());
    this.inputRef.focus();
  };

  handleKeyDownInput = (e) => {
    if (e.keyCode === 13) {
      this.send();
    }
  };

  render() {
    const { inputValue, dispatch } = this.props;

    return (
      <div className="up-room__chat__input">
        <input
          type="text"
          value={inputValue}
          placeholder="Write message"
          onKeyDown={this.handleKeyDownInput}
          onChange={(e) => { dispatch(roomInputChange(e.target.value)); }}
          ref={(ref) => { this.inputRef = ref; }}
        />
        <IconButton
          title="Attach File"
          aria-label="Attach File"
          onClick={() => { this.send(); }}
        >
          <AttachFile />
        </IconButton>
        <IconButton
          title="Send"
          aria-label="Send"
          onClick={() => { this.send(); }}
        >
          <Send />
        </IconButton>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.room);
}

export default connect(mapStateToProps)(MessageInput);
