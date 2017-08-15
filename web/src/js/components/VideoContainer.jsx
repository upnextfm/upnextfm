import React from 'react';
import VideoPlayer from 'components/VideoPlayer';
import VideoButtons from 'components/VideoButtons';
import Playlist from 'components/Playlist';

class VideoContainer extends React.Component {
  render() {
    return (
      <div className="up-room__video">
        <VideoPlayer />
        <VideoButtons />
        <Playlist />
      </div>
    );
  }
}

export default VideoContainer;
