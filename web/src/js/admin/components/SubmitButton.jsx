import React from 'react';
import PropTypes from 'prop-types';
import Loader from 'components/Loader';

const SubmitButton = ({ isSubmitting, children, ...props }) => (
  <button className="btn upa-btn-secondary" type="submit" disabled={isSubmitting} {...props}>
    {isSubmitting ? <Loader size="small" isCentered={false} /> : children}
  </button>
);

SubmitButton.propTypes = {
  isSubmitting: PropTypes.bool,
  children:     PropTypes.node
};

SubmitButton.defaultProps = {
  isSubmitting: false,
  children:     ''
};

export default SubmitButton;
