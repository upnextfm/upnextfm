import React from 'react';
import PropTypes from 'prop-types';
import { Scrollbars } from 'react-custom-scrollbars';
import { usersFindByUsername } from 'utils/users';
import Dropzone from 'react-dropzone';
import List from 'material-ui/List';
import MessageType from 'components/Chat/Types/MessageType';
import NoticeType from 'components/Chat/Types/NoticeType';

export default class MessagesPanel extends React.PureComponent {
  static propTypes = {
    messages: PropTypes.array,
    users:    PropTypes.array,
    settings: PropTypes.object,
    onUpload: PropTypes.func
  };

  static defaultProps = {
    onUpload: () => {}
  };

  constructor(props) {
    super(props);
    this.dropzoneRef = null;
  }

  componentDidUpdate(prevProps) {
    if (prevProps.messages.length !== this.props.messages.length) {
      this.scrollToBottom();
    }
  }

  scrollToBottom = () => {
    setTimeout(() => {
      this.scrollRef.scrollToBottom();
    }, 10);
  };

  openUpload = () => {
    this.dropzoneRef.open();
  };

  handleDropFile = (files) => {
    if (files.length > 0) {
      this.props.onUpload(files[0]);
    } else {
      console.error('No acceptable files received.');
    }
  };

  render() {
    const { messages, users, settings } = this.props;
    let prevUser = null;
    let prevMessage = null;

    return (
      <Dropzone
        className="up-room-dropzone"
        onDrop={this.handleDropFile}
        maxSize={settings.site.maxUploadSize}
        ref={(ref) => { this.dropzoneRef = ref; }}
        disableClick
        disablePreview
      >
        <Scrollbars ref={(ref) => { this.scrollRef = ref; }}>
          <List className="up-room-panel__messages">
            {messages.map((message) => {
              let item;
              const user = usersFindByUsername(users, message.from);
              if (message.type === 'message') {
                item = (
                  <MessageType
                    key={message.id}
                    user={user}
                    message={message}
                    onContextMenu={this.handleContextMenuUser}
                    prevMessage={prevMessage}
                    prevUser={prevUser}
                  />
                );
              } else if (message.type === 'notice' && settings.user.showNotices) {
                item = (
                  <NoticeType
                    key={message.id}
                    user={user}
                    message={message}
                  />
                );
              }

              prevUser    = user;
              prevMessage = message;
              return item;
            })}
          </List>
        </Scrollbars>
      </Dropzone>
    );
  }
}

