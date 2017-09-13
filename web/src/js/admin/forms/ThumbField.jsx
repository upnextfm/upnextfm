import React from 'react';
import Dropzone from 'react-dropzone';

function getThumbSrc(entity, size) {
  if (!entity.data.settings) {
    return '';
  }

  switch (size) {
    case 'sm':
      return entity.data.settings.thumbSm;
    case 'md':
      return entity.data.settings.thumbMd;
    case 'lg':
      return entity.data.settings.thumbLg;
    default:
      return console.error(`Invalid thumb size "${size}".`);
  }
}

class ThumbField extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      source: getThumbSrc(props.entity, 'lg')
    };
  }

  componentDidUpdate(prevProps) {
    if (prevProps.entity.data !== this.props.entity.data) {
      this.setState({ source: getThumbSrc(this.props.entity, 'lg') }); // eslint-disable-line
    }
  }

  handleFileDrop = (files) => {
    if (files.length === 0) {
      return;
    }
    this.props.input.onChange(files[0]);
    this.setState({ source: files[0].preview });
  };

  render() {
    const { input, meta } = this.props;
    const { source } = this.state;

    let classNames  = 'up-avatar';
    const id        = `input-${input.name.replace('.', '-')}`;
    const isInvalid = meta.touched && meta.error;
    if (isInvalid) {
      classNames = 'up-avatar invalid';
    }

    return (
      <Dropzone
        accept="image/*"
        className="input-field"
        onDrop={this.handleFileDrop}
        multiple={false}
      >
        <label htmlFor={id} data-error={meta.error}>
          Thumbnail
        </label>
        <img
          id={id}
          className={classNames}
          src={source}
          alt="Thumbnail"
        />
      </Dropzone>
    );
  }
}

export default ThumbField;
