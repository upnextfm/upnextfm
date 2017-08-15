import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Grid from 'material-ui/Grid';
import { roomSetName } from 'actions/roomActions';
import { objectKeyFilter } from 'utils/objects';
import LoginDialog from 'components/Dialogs/LoginDialog';
import RegisterDialog from 'components/Dialogs/RegisterDialog';
import Nav from 'components/Nav';
import ChatContainer from 'components/ChatContainer';
import VideoContainer from 'components/VideoContainer';


class Room extends React.Component {
  static propTypes = {
    name:     PropTypes.string.isRequired,
    auth:     PropTypes.object,
    register: PropTypes.object,
    room:     PropTypes.object,
    nav:      PropTypes.object,
    dispatch: PropTypes.func
  };

  static defaultProps = {
    dispatch: () => {}
  };

  constructor(props) {
    super(props);
    props.dispatch(roomSetName(props.name));
  }

  render() {
    const { auth, ...props } = this.props;

    return (
      <div {...objectKeyFilter(props, Room.propTypes)}>
        <Nav auth={auth} />
        <div className="up-room">
          <Grid item xs={12} sm={12} md={7}>
            <ChatContainer />
          </Grid>
          <Grid item xs={12} sm={12} md={5}>
            <VideoContainer />
          </Grid>
        </div>
        <LoginDialog />
        <RegisterDialog />
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state);
}

export default connect(mapStateToProps)(Room);
