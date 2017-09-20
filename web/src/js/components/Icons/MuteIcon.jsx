import React from 'react';
import PropTypes from 'prop-types';
import Icon from 'components/Icon';

const MuteIcon = ({ isMuted, ...props }) => (
  isMuted ? <Icon name="volume_off" {...props} /> : <Icon name="volume_up" {...props} />
);

MuteIcon.propTypes = {
  isMuted: PropTypes.bool
};

MuteIcon.defaultProps = {
  isMuted: false
};

export default MuteIcon;
