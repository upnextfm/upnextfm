import React from 'react';
import PropTypes from 'prop-types';

const Loader = ({ isCentered }) => (
  <div className={isCentered && 'up-center-page'}>
    <div className="preloader-wrapper big active">
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
  isCentered: PropTypes.bool
};

Loader.defaultProps = {
  isCentered: true
};

export default Loader;
