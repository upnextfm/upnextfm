import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { usersFindByUsername } from 'utils/users';
import Hidden from 'material-ui/Hidden';
import List, { ListItem } from 'material-ui/List';
import IconButton from 'material-ui/IconButton';
import KeyboardArrowLeft from 'material-ui-icons/KeyboardArrowLeft';
import User from 'components/Chat/User';

export default class UsersPanel extends React.Component {
  static propTypes = {
    roomUsers:   PropTypes.array,
    repoUsers:   PropTypes.array,
    isCollapsed: PropTypes.bool,
    onCollapse:  PropTypes.func
  };

  static defaultProps = {
    roomUsers:   [],
    repoUsers:   [],
    isCollapsed: false,
    onCollapse:  () => {}
  };

  render() {
    const { roomUsers, repoUsers, isCollapsed, onCollapse } = this.props;

    return (
      <div className={classNames(
        'up-room-panel__users',
        {
          'up-collapsed': isCollapsed
        }
      )}
      >
        <List>
          {roomUsers.map(username => (
            <ListItem key={username} button>
              <User user={usersFindByUsername(repoUsers, username)} />
            </ListItem>
          ))}
        </List>
        <Hidden xsDown>
          <div className="up-room-users__controls">
            <IconButton className="up-collapse" onClick={onCollapse}>
              <KeyboardArrowLeft className={classNames(
                'up-collapse__icon',
                {
                  'up-collapsed': isCollapsed
                }
               )}
              />
            </IconButton>
          </div>
        </Hidden>
      </div>
    );
  }
}

