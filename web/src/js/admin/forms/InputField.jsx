import React from 'react';

const InputField = ({ input, label, type, meta }) => {
  let classNames  = '';
  const id        = `input-${input.name.replace('.', '-')}`;
  const isInvalid = meta.touched && meta.error;
  if (isInvalid) {
    classNames = 'invalid';
  }

  return (
    <div className={type !== 'checkbox' ? 'input-field' : ''}>
      {type === 'textarea' ? (
        <textarea
          {...input}
          id={id}
          className={`materialize-textarea ${classNames}`}
        />
      ) : (
        <input
          {...input}
          id={id}
          type={type}
          className={classNames}
        />
      )}
      <label htmlFor={id} data-error={meta.error}>
        {label}
      </label>
    </div>
  );
};

export default InputField;
