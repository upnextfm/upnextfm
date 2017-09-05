import React from 'react';
import PropTypes from 'prop-types';
import Menu, { MenuItem } from 'material-ui/Menu';

export default class PlaylistMenu extends React.Component {
  static propTypes = {
    anchor:         PropTypes.any,
    isOpen:         PropTypes.bool,
    onRequestClose: PropTypes.func
  };

  static defaultProps = {
    anchor:         undefined,
    isOpen:         false,
    onRequestClose: () => {}
  };

  render() {
    const { isOpen, anchor, onRequestClose } = this.props;

    return (
      <Menu
        anchorEl={anchor}
        open={isOpen}
        onRequestClose={onRequestClose}
      >
        <MenuItem onClick={onRequestClose}>
          Remove
        </MenuItem>
        <MenuItem onClick={onRequestClose}>
          Play Next
        </MenuItem>
      </Menu>
    );
  }
}
