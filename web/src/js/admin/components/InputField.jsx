import React from 'react';
import PropTypes from 'prop-types';
import { Field } from 'redux-form';

const InputField = ({ name, label, component, type }) => (
  <div className="input-field">
    <label htmlFor={`input-${name}`}>
      {label}
    </label>
    <Field name={name} id={`input-${name}`} component={component} type={type} />
  </div>
);

InputField.propTypes = {
  name:      PropTypes.string.isRequired,
  label:     PropTypes.string.isRequired,
  component: PropTypes.string,
  type:      PropTypes.string
};

InputField.defaultProps = {
  component: 'input',
  type:      'text'
};

export default InputField;
