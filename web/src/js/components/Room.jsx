import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Hidden from 'material-ui/Hidden';
import Grid from 'material-ui/Grid';
import { settingsSocket } from 'actions/settingsActions';
import { userUsername } from 'actions/userActions';
import { search, searchClear, searchTerm } from 'actions/searchActions';
import { playlistAppend } from 'actions/playlistActions';
import {
  layoutToggleLoginDialog,
  layoutToggleRegisterDialog,
  layoutToggleHelpDialog,
  layoutToggleRoomSettingsDialog,
  layoutErrorMessage
} from 'actions/layoutActions';
import ErrorSnackbar from 'components/ErrorSnackbar';
import Progress from 'components/Video/Progress';
import RoomSettingsDialog from 'components/Dialogs/RoomSettingsDialog';
import HelpDialog from 'components/Dialogs/HelpDialog';
import LoginDialog from 'components/Dialogs/LoginDialog';
import SearchResultsDialog from 'components/Dialogs/SearchResultsDialog';
import RegisterDialog from 'components/Dialogs/RegisterDialog';
import ChatSide from 'components/Chat/ChatSide';
import VideoSide from 'components/Video/VideoSide';
import VideoNav from 'components/VideoNav';
import Nav from 'components/Nav';

class Room extends React.PureComponent {
  static propTypes = {
    roomName:       PropTypes.string.isRequired,
    socketSettings: PropTypes.object.isRequired,
    username:       PropTypes.string,
    user:           PropTypes.object,
    layout:         PropTypes.object
  };

  constructor(props) {
    super(props);
    props.dispatch(settingsSocket(props.socketSettings));
    props.dispatch(userUsername(props.username));
  }

  componentDidCatch(error) {
    console.error(error);
  }

  handleCloseErrorSnackbar = () => {
    this.props.dispatch(layoutErrorMessage(''));
  };

  handleClickSearchResultsAdd = (item) => {
    const permalink = `https://youtu.be/${item.id.videoId}`;
    this.props.dispatch(playlistAppend(permalink));
  };

  handleChangeSearchResults = (e) => {
    this.props.dispatch(searchTerm(e.target.value));
  };

  handleSubmitSearchResults = (term) => {
    this.props.dispatch(search(term));
  };

  render() {
    const { roomName, user, layout, search, dispatch } = this.props;

    return (
      <div>
        <Nav user={user} roomName={roomName} />
        <Hidden smUp>
          <VideoNav />
        </Hidden>
        <Hidden smUp>
          <Progress className="up-video-progress--thin" />
        </Hidden>
        <div className="up-room">
          <Grid item xs={12} md={layout.colsChatSide}>
            <ChatSide roomName={roomName} />
          </Grid>
          <Grid item xs={12} md={layout.colsVideoSide}>
            <VideoSide />
          </Grid>
        </div>
        <ErrorSnackbar
          errorMessage={layout.errorMessage}
          errorDuration={layout.errorDuration}
          onClose={this.handleCloseErrorSnackbar}
        />
        <HelpDialog
          isOpen={layout.isHelpDialogOpen}
          onClose={() => { dispatch(layoutToggleHelpDialog()); }}
        />
        <RoomSettingsDialog
          isOpen={layout.isRoomSettingsDialogOpen}
          onClose={() => { dispatch(layoutToggleRoomSettingsDialog()); }}
        />
        <LoginDialog
          isOpen={layout.isLoginDialogOpen}
          onClose={() => { dispatch(layoutToggleLoginDialog()); }}
        />
        <RegisterDialog
          isOpen={layout.isRegisterDialogOpen}
          onClose={() => { dispatch(layoutToggleRegisterDialog()); }}
        />
        <SearchResultsDialog
          isOpen={search.results.length > 0}
          searchTerm={search.term}
          searchResults={search.results}
          onSubmit={this.handleSubmitSearchResults}
          onChange={this.handleChangeSearchResults}
          onClickAdd={this.handleClickSearchResultsAdd}
          onClose={() => { dispatch(searchClear()); }}
        />
      </div>
    );
  }
}

function mapStateToProps(state) {
  return {
    user:   Object.assign({}, state.user),
    layout: Object.assign({}, state.layout),
    search: Object.assign({}, state.search)
  };
}

export default connect(mapStateToProps)(Room);
