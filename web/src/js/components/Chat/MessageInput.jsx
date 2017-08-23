import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import IconButton from 'material-ui/IconButton';
import Send from 'material-ui-icons/Send';
import AttachFile from 'material-ui-icons/AttachFile';
import { roomInputChange, roomSend } from 'actions/roomActions';

const KEY_TAB   = 9;
const KEY_ENTER = 13;
const KEY_UP    = 38;
const KEY_DOWN  = 40;

class MessageInput extends React.Component {
  static propTypes = {
    dispatch: PropTypes.func
  };

  static defaultProps = {
    dispatch: () => {}
  };

  constructor(props) {
    super(props);
    this.history       = [];
    this.historyIndex  = 0;
    this.historyMoving = false;
  }

  componentDidMount() {
    setTimeout(() => {
      this.inputRef.focus();
    }, 1000);
  }

  componentDidUpdate() {
    if (this.historyMoving) {
      this.historyMoving = false;
      this.moveCaretToEnd();
    }
  }

  send = () => {
    this.history.push(this.props.inputValue);
    this.historyIndex = this.history.length;
    this.props.dispatch(roomSend());
    this.inputRef.focus();
  };

  moveCaretToEnd = () => {
    setTimeout(() => {
      this.inputRef.focus();
      this.inputRef.selectionStart = this.inputRef.selectionEnd = 10000; // eslint-disable-line
    }, 0);
  };

  handleKeyDownInput = (e) => {
    switch (e.keyCode) { // eslint-disable-line default-case
      case KEY_ENTER:
        this.send();
        break;
      case KEY_TAB:
        if (this.inputValue !== '') {
          for (let i = 0; i < this.props.users.length; i++) {
            if (this.props.users[i].toLowerCase().indexOf(this.props.inputValue.toLowerCase()) === 0) {
              this.historyMoving = true;
              this.props.dispatch(roomInputChange(
                `${this.props.users[i]} `
              ));
            }
          }
        }
        break;
      case KEY_UP:
        if (this.historyIndex > 0) {
          this.historyIndex -= 1;
          this.historyMoving = true;
          this.props.dispatch(roomInputChange(
            this.history[this.historyIndex]
          ));
        }
        break;
      case KEY_DOWN:
        if (this.history.length > this.historyIndex + 1) {
          this.historyIndex += 1;
          this.historyMoving = true;
          this.props.dispatch(roomInputChange(
            this.history[this.historyIndex]
          ));
        } else if (this.history.length === this.historyIndex + 1) {
          this.props.dispatch(roomInputChange(''));
        }
        break;
    }
  };

  render() {
    const { inputValue, dispatch } = this.props;

    return (
      <div className="up-room-messages__input">
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
          style={{ marginLeft: 6 }}
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
