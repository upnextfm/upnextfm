import React from 'react';
import PropTypes from 'prop-types';

class Pagination extends React.Component {
  static propTypes = {
    page:    PropTypes.number.isRequired,
    total:   PropTypes.number.isRequired,
    onClick: PropTypes.func
  };

  static defaultProps = {
    onClick: () => {}
  };

  render() {
    const { page, total, onClick } = this.props;

    const links = [];
    for (let i = 1; i <= total; i++) {
      links.push(
        <li key={i} className={page === i && 'active'}>
          <span onClick={(e) => { onClick(e, i); }}>{i}</span>
        </li>
      );
    }

    return (
      <ul className="pagination">
        <li className={page === 1 && 'disabled'}>
          <span onClick={(e) => { onClick(e, (page - 1 < 1 ? 1 : page - 1)); }}>
            <i className="material-icons">chevron_left</i>
          </span>
        </li>
        {links}
        <li className={page === total && 'disabled'}>
          <span onClick={(e) => { onClick(e, (page + 1 <= total ? page + 1 : page)); }}>
            <i className="material-icons">chevron_right</i>
          </span>
        </li>
      </ul>
    );
  }
}

export default Pagination;
