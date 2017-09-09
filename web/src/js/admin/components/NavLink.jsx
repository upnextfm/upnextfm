import React from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';

class NavLink extends React.Component {
  static contextTypes = {
    router: PropTypes.object
  };

  render() {
    const isActive = this.context.router.route.location.pathname === this.props.to;

    return (
      <li className={isActive ? 'active' : ''}>
        <Link {...this.props}>
          {this.props.children}
        </Link>
      </li>
    );
  }
}

export default NavLink;
