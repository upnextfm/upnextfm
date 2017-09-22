import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { childrenRecursiveMap } from 'utils/props';
import { objectKeyFilter } from 'utils/objects';

class InputGroup extends React.Component {
  static propTypes = {
    label:     PropTypes.string,
    htmlFor:   PropTypes.string,
    error:     PropTypes.string,
    className: PropTypes.string,
    children:  PropTypes.node
  };

  static defaultProps = {
    label:     '',
    htmlFor:   '',
    error:     '',
    className: '',
    children:  ''
  };

  render() {
    const { label, error, className, children, ...props } = this.props;
    let { htmlFor } = this.props;

    if (htmlFor === '') {
      childrenRecursiveMap(children, (child) => {
        if (child.props.id !== undefined && htmlFor === '') {
          htmlFor = child.props.id;
        }
      });
    }

    return (
      <div
        className={classNames('up-form-group input-field', className)}
        {...objectKeyFilter(props, InputGroup.propTypes)}
      >
        {label && <label htmlFor={htmlFor}>{label}</label>}
        {error && <div className="up-form-error">{error}</div>}
        {children}
      </div>
    );
  }
}

export default InputGroup;
