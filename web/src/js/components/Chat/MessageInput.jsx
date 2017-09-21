import React from 'react';
import PropTypes from 'prop-types';
import IconButton from 'material-ui/IconButton';
import { ChromePicker } from 'react-color';
import Icon from 'components/Icon';

const KEY_TAB   = 9;
const KEY_ENTER = 13;
const KEY_UP    = 38;
const KEY_DOWN  = 40;

export default class MessageInput extends React.PureComponent {
  static propTypes = {
    settings:      PropTypes.object,
    tabComplete:   PropTypes.array,
    onSend:        PropTypes.func,
    onAttach:      PropTypes.func,
    onColorChange: PropTypes.func
  };

  static defaultProps = {
    tabComplete:   [],
    onSend:        () => {},
    onAttach:      () => {},
    onColorChange: () => {}
  };

  constructor(props) {
    super(props);
    this.state = {
      value:      '',
      pickerOpen: false
    };

    this.inputRef      = null;
    this.history       = [];
    this.historyIndex  = 0;
    this.historyMoving = false;
  }

  componentDidMount() {
    this.focus();
    document.addEventListener('click', this.handleDocumentClick);
  }

  componentDidUpdate() {
    if (this.historyMoving) {
      this.historyMoving = false;
      this.moveCaretToEnd();
    }
  }

  componentWillUnmount() {
    document.removeEventListener('click', this.handleDocumentClick);
  }

  focus = () => {
    setTimeout(() => {
      this.inputRef.focus();
    }, 500);
  };

  send = () => {
    const value = this.state.value.substr(0, this.props.settings.site.maxInputChars);
    this.history.push(value);
    this.historyIndex = this.history.length;
    this.props.onSend(value);
    this.setState({ value: '' }, () => {
      this.inputRef.focus();
    });
  };

  moveCaretToEnd = () => {
    setTimeout(() => {
      this.inputRef.focus();
      this.inputRef.selectionStart = this.inputRef.selectionEnd = 10000; // eslint-disable-line
    }, 0);
  };

  handleKeyDownInput = (e) => {
    const { value, tabComplete } = this.props;

    switch (e.keyCode) { // eslint-disable-line default-case
      case KEY_ENTER:
        this.send();
        break;
      case KEY_TAB:
        if (value !== '') {
          for (let i = 0; i < tabComplete.length; i++) {
            if (tabComplete[i].toLowerCase().indexOf(value.toLowerCase()) === 0) {
              this.historyMoving = true;
              this.setState({ value: `${tabComplete[i]} ` });
            }
          }
        }
        break;
      case KEY_UP:
        if (this.historyIndex > 0) {
          this.historyIndex -= 1;
          this.historyMoving = true;
          this.setState({ value: this.history[this.historyIndex] });
        }
        break;
      case KEY_DOWN:
        if (this.history.length > this.historyIndex + 1) {
          this.historyIndex += 1;
          this.historyMoving = true;
          this.setState({ value: this.history[this.historyIndex] });
        } else if (this.history.length === this.historyIndex + 1) {
          this.setState({ value: '' });
        }
        break;
    }
  };

  handleChangeTextColor = (e) => {
    this.props.onColorChange(e.hex);
  };

  handleClickSwatch = () => {
    this.setState({ pickerOpen: !this.state.pickerOpen });
  };

  handleDocumentClick = (e) => {
    if (!e.target.classList.contains('up-room-color__swatch')) {
      this.setState({ pickerOpen: false });
    }
  };

  handleClickPicker = (e) => {
    e.stopPropagation();
  };

  renderButtonColor() {
    const { settings } = this.props;
    const { pickerOpen } = this.state;

    return (
      <div
        className="up-room-color__swatch"
        style={{ backgroundColor: settings.user.textColor, marginLeft: 6 }}
        onClick={this.handleClickSwatch}
      >
        {pickerOpen && (
          <div className="up-room-color__container" onClick={this.handleClickPicker}>
            <ChromePicker
              color={settings.user.textColor}
              className="up-room-color__picker"
              onChangeComplete={this.handleChangeTextColor}
              disableAlpha
            />
          </div>
        )}
      </div>
    );
  }

  renderButtonAttach() {
    return (
      <IconButton
        title="Attach File"
        aria-label="Attach File"
        style={{ marginLeft: 6 }}
        onClick={() => { this.props.onAttach(); }}
      >
        <Icon name="attach_file" />
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
        <Icon name="send" />
      </IconButton>
    );
  }

  render() {
    const { settings } = this.props;
    const { value } = this.state;

    return (
      <div className="up-room-messages__input">
        <input
          type="text"
          value={value}
          placeholder="Write message"
          maxLength={String(settings.site.maxInputChars)}
          onChange={(e) => { this.setState({ value: e.target.value }); }}
          onKeyDown={this.handleKeyDownInput}
          ref={(ref) => { this.inputRef = ref; }}
        />
        {this.renderButtonColor()}
        {this.renderButtonAttach()}
        {this.renderButtonSend()}
      </div>
    );
  }
}
