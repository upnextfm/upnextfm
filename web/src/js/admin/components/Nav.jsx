import React from 'react';
import { Link } from 'react-router-dom';
import NavLink from 'admin/components/NavLink';

export default class Nav extends React.Component {

  render() {
    return (
      <nav>
        <div className="nav-wrapper upa-nav-wrapper">
          <Link to="/" className="brand-logo">
            <img src="/images/logo-brand.png" alt="Logo" />
          </Link>
          <ul id="nav-mobile" className="upa-buttons hide-on-med-and-down">
            <NavLink to="/users">Users</NavLink>
            <NavLink to="/rooms">Rooms</NavLink>
            <NavLink to="/playlists">Playlists</NavLink>
            <li>
              <a className="dropdown-button" href="#!" data-activates="dropdown-content">
                Content<i className="material-icons right">arrow_drop_down</i>
              </a>
            </li>
            <NavLink to="/blog">Blog</NavLink>
          </ul>
        </div>
      </nav>
    );
  }
}
