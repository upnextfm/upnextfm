import React from 'react';
import PropTypes from 'prop-types';

const Card = ({ title, actions, children, ...props }) => (
  <div className="card upa-card" {...props}>
    <div className="card-content">
      {title && (
        <span className="card-title">
          {title}
        </span>
      )}
      {children}
    </div>
    {actions && (
      <div className="card-action">
        {actions}
      </div>
    )}
  </div>
);

Card.propTypes = {
  title:    PropTypes.string,
  actions:  PropTypes.node,
  children: PropTypes.node
};

Card.defaultProps = {
  title:    '',
  actions:  '',
  children: ''
};

export default Card;
