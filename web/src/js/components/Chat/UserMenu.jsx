import React from 'react';
import PropTypes from 'prop-types';
import Menu, { MenuItem } from 'material-ui/Menu';

export default class UserMenu extends React.Component {
  static propTypes = {
    anchor:         PropTypes.any,
    isOpen:         PropTypes.bool,
    onClickProfile: PropTypes.func,
    onRequestClose: PropTypes.func
  };

  static defaultProps = {
    anchor:         undefined,
    isOpen:         false,
    onClickProfile: () => {},
    onRequestClose: () => {}
  };

  render() {
    const { isOpen, anchor, onRequestClose, onClickProfile } = this.props;

    return (
      <Menu
        anchorEl={anchor}
        open={isOpen}
        onRequestClose={onRequestClose}
      >
        <MenuItem onClick={onRequestClose}>
          Private Message
        </MenuItem>
        <MenuItem onClick={onClickProfile}>
          Profile
        </MenuItem>
        <MenuItem onClick={onRequestClose}>
          Ignore
        </MenuItem>
      </Menu>
    );
  }
}
