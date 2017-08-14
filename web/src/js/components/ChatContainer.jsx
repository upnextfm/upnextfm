import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { roomInputChange, roomInputSend } from 'actions/roomActions';
import Paper from 'material-ui/Paper';
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
      <Paper elevation = {4} className = "up-room__paper_container up-room__chat">
        <div className="up-room__chat__users">
          Users
        </div>
        <div className="up-room__chat__messages">
          <div className="up-room__chat__scroll">
            Messages
          </div>
          <div className="up-room__chat__input">
            <input
              type="text"
              value={inputValue}
              onKeyDown={this.handleKeyDownInput}
              onChange={(e) => { dispatch(roomInputChange(e.target.value)); }}
              ref={(ref) => { this.inputRef = ref; }}
            />
          </div>
        </div>
      </Paper>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.room);
}

export default connect(mapStateToProps)(ChatContainer);
