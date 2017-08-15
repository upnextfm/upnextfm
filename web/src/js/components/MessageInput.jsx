import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Button from 'material-ui/Button';
import Send from 'material-ui-icons/Send';
import { roomInputChange, roomInputSend } from 'actions/roomActions';

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

  handleKeyDownInput = (e) => {
    if (e.keyCode === 13) {
      this.props.dispatch(roomInputSend());
    }
  };

  render() {
    const { inputValue, dispatch } = this.props;

    return (
      <div className="up-room__chat__input up-paper-container">
        <input
          type="text"
          value={inputValue}
          onKeyDown={this.handleKeyDownInput}
          onChange={(e) => { dispatch(roomInputChange(e.target.value)); }}
          ref={(ref) => { this.inputRef = ref; }}
        />
        <Button
          color="accent"
          className="up-room-btn-send"
          title="Send"
          aria-label="Send"
          onClick={() => { dispatch(roomInputSend()); }}
          fab
        >
          <Send />
        </Button>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.room);
}

export default connect(mapStateToProps)(MessageInput);
