import React from 'react';

const InputField = ({ input, label, type, meta }) => {
  let classNames  = '';
  const id        = `input-${input.name.replace('.', '-')}`;
  const isInvalid = meta.touched && meta.error;
  if (isInvalid) {
    classNames = 'invalid';
  }

  const labelComponent = (
    <label htmlFor={id} data-error={meta.error}>
      {label}
    </label>
  );

  switch (type) {
    case 'textarea':
      return (
        <div className="input-field">
          <textarea
            {...input}
            id={id}
            className={`materialize-textarea ${classNames}`}
          />
          {labelComponent}
        </div>
      );
      break;
    case 'checkbox':
      return (
        <div>
          <input
            {...input}
            id={id}
            type={type}
            checked={input.value}
            className={classNames}
          />
          {labelComponent}
        </div>
      );
      break;
    default:
      return (
        <div className="input-field">
          <input
            {...input}
            id={id}
            type={type}
            className={classNames}
          />
          {labelComponent}
        </div>
      );
      break;
  }


};

export default InputField;
