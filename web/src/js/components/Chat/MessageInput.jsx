import React from 'react';
import PropTypes from 'prop-types';
import IconButton from 'material-ui/IconButton';
import Send from 'material-ui-icons/Send';
import AttachFile from 'material-ui-icons/AttachFile';

const KEY_TAB   = 9;
const KEY_ENTER = 13;
const KEY_UP    = 38;
const KEY_DOWN  = 40;

export default class MessageInput extends React.Component {
  static propTypes = {
    value:       PropTypes.string,
    settings:    PropTypes.object,
    tabComplete: PropTypes.array,
    onSend:      PropTypes.func,
    onAttach:    PropTypes.func,
    onChange:    PropTypes.func
  };

  static defaultProps = {
    value:       '',
    tabComplete: [],
    onSend:      () => {},
    onAttach:    () => {},
    onChange:    () => {}
  };

  constructor(props) {
    super(props);

    this.inputRef      = null;
    this.history       = [];
    this.historyIndex  = 0;
    this.historyMoving = false;
  }

  componentDidMount() {
    this.focus();
  }

  componentDidUpdate() {
    if (this.historyMoving) {
      this.historyMoving = false;
      this.moveCaretToEnd();
    }
  }

  focus = () => {
    setTimeout(() => {
      this.inputRef.focus();
    }, 500);
  };

  send = () => {
    const value = this.props.value.substr(0, this.props.settings.site.maxInputChars);
    this.history.push(value);
    this.historyIndex = this.history.length;
    this.props.onSend();
    this.inputRef.focus();
  };

  moveCaretToEnd = () => {
    setTimeout(() => {
      this.inputRef.focus();
      this.inputRef.selectionStart = this.inputRef.selectionEnd = 10000; // eslint-disable-line
    }, 0);
  };

  handleChange = (e) => {
    this.props.onChange(e.target.value);
  };

  handleKeyDownInput = (e) => {
    const { value, tabComplete, onChange } = this.props;

    switch (e.keyCode) { // eslint-disable-line default-case
      case KEY_ENTER:
        this.send();
        break;
      case KEY_TAB:
        if (value !== '') {
          for (let i = 0; i < tabComplete.length; i++) {
            if (tabComplete[i].toLowerCase().indexOf(value.toLowerCase()) === 0) {
              this.historyMoving = true;
              onChange(`${tabComplete[i]} `);
            }
          }
        }
        break;
      case KEY_UP:
        if (this.historyIndex > 0) {
          this.historyIndex -= 1;
          this.historyMoving = true;
          onChange(this.history[this.historyIndex]);
        }
        break;
      case KEY_DOWN:
        if (this.history.length > this.historyIndex + 1) {
          this.historyIndex += 1;
          this.historyMoving = true;
          onChange(this.history[this.historyIndex]);
        } else if (this.history.length === this.historyIndex + 1) {
          onChange('');
        }
        break;
    }
  };

  renderButtonAttach() {
    return (
      <IconButton
        title="Attach File"
        aria-label="Attach File"
        style={{ marginLeft: 6 }}
        onClick={() => { this.props.onAttach(); }}
      >
        <AttachFile />
      </IconButton>
    );
  }

  renderButtonSend() {
    return (
      <IconButton
        title="Send"
        aria-label="Send"
        onClick={() => { this.send(); }}
      >
        <Send />
      </IconButton>
    );
  }

  render() {
    const { value, settings } = this.props;

    return (
      <div className="up-room-messages__input">
        <input
          type="text"
          value={value}
          placeholder="Write message"
          maxLength={String(settings.site.maxInputChars)}
          onChange={this.handleChange}
          onKeyDown={this.handleKeyDownInput}
          ref={(ref) => { this.inputRef = ref; }}
        />
        {this.renderButtonAttach()}
        {this.renderButtonSend()}
      </div>
    );
  }
}

