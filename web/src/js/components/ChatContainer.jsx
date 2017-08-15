import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { roomInputChange, roomInputSend } from 'actions/roomActions';
import Users from 'components/Users';
import Messages from 'components/Messages';

class ChatContainer extends React.Component {
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
    const { dispatch, inputValue } = this.props;

    return (
      <div className="up-room__chat">
        <Users />
        <div className="up-room__chat__messages-container">
          <Messages />
          <div className="up-room__chat__input up-paper-container">
            <input
              type="text"
              value={inputValue}
              onKeyDown={this.handleKeyDownInput}
              onChange={(e) => { dispatch(roomInputChange(e.target.value)); }}
              ref={(ref) => { this.inputRef = ref; }}
            />
          </div>
        </div>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.room);
}

export default connect(mapStateToProps)(ChatContainer);
