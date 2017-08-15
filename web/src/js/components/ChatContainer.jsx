import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { roomInputChange, roomInputSend } from 'actions/roomActions';
import UsersContainer from 'components/UsersContainer';
import MessagesContainer from 'components/MessagesContainer';

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
        <UsersContainer />
        <div className="up-room__chat__messages-container">
          <MessagesContainer />
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
