import React from 'react';
import Dropzone from 'react-dropzone';

function getAvatarSrc(entity, size) {
  if (!entity.data.info) {
    return '';
  }

  switch (size) {
    case 'sm':
      return entity.data.info.avatarSm;
    case 'md':
      return entity.data.info.avatarMd;
    case 'lg':
      return entity.data.info.avatarLg;
    default:
      return console.error(`Invalid avatar size "${size}".`);
  }
}

class AvatarField extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      source: getAvatarSrc(props.entity, 'lg')
    };
  }

  componentDidUpdate(prevProps) {
    if (prevProps.entity.data !== this.props.entity.data) {
      this.setState({ source: getAvatarSrc(this.props.entity, 'lg') }); // eslint-disable-line
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
          Avatar
        </label>
        <img
          id={id}
          className={classNames}
          src={source}
          alt="Avatar"
        />
      </Dropzone>
    );
  }
}

export default AvatarField;
