import React from 'react';
import PropTypes from 'prop-types';
import Menu, { MenuItem } from 'material-ui/Menu';

export default class PlaylistMenu extends React.Component {
  static propTypes = {
    videoID:        PropTypes.number,
    anchor:         PropTypes.any,
    isOpen:         PropTypes.bool,
    permissions:    PropTypes.object,
    onClick:        PropTypes.func,
    onRequestClose: PropTypes.func
  };

  static defaultProps = {
    anchor:      undefined,
    isOpen:      false,
    permissions: {
      remove: false,
      vote: false
    },
    onClick:        () => {},
    onRequestClose: () => {}
  };

  render() {
    const { videoID, isOpen, anchor, permissions, onClick, onRequestClose } = this.props;

    return (
      <Menu
        anchorEl={anchor}
        open={isOpen}
        onRequestClose={onRequestClose}
      >    
        {!permissions.remove ? null : (
          <MenuItem onClick={(e) => { onClick(e, 'remove', videoID); }}>
            Remove
          </MenuItem>
        )}
        {!permissions.playNext ? null : (
          <MenuItem onClick={(e) => { onClick(e, 'playNext', videoID); }}>
            Play Next
          </MenuItem>
        )}
      </Menu>
    );
  }
}
