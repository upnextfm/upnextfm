import React from 'react';
import PropTypes from 'prop-types';

const Loader = ({ size, isCentered }) => (
  <div className={isCentered && 'up-center-page'}>
    <div className={`preloader-wrapper ${size} active`}>
      <div className="spinner-layer upa-spinner-primary">
        <div className="circle-clipper left">
          <div className="circle" />
        </div>
        <div className="gap-patch">
          <div className="circle" />
        </div>
        <div className="circle-clipper right">
          <div className="circle" />
        </div>
      </div>
    </div>
  </div>
);

Loader.propTypes = {
  size:       PropTypes.oneOf(['small', 'medium', 'big']),
  isCentered: PropTypes.bool
};

Loader.defaultProps = {
  size:       'big',
  isCentered: true
};

export default Loader;
