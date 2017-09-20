import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { withStyles, createStyleSheet } from 'material-ui/styles';
import { CircularProgress } from 'material-ui/Progress';
import green from 'material-ui/colors/green';
import Button from 'material-ui/Button';
import Icon from 'components/Icon';

const styleSheet = createStyleSheet({
  wrapper: {
    position: 'relative'
  },
  successButton: {
    backgroundColor: green[500],
    '&:hover':       {
      backgroundColor: green[700]
    }
  },
  progress: {
    color:    green[500],
    position: 'absolute',
    top:      -2,
    left:     -2
  }
});

class CircularFab extends Component {
  static propTypes = {
    classes: PropTypes.object.isRequired,
    loading: PropTypes.bool,
    success: PropTypes.bool,
    onClick: PropTypes.func
  };

  static defaultProps = {
    loading: false,
    success: false,
    onClick: () => {}
  };

  render() {
    const { loading, success } = this.props;
    const classes = this.props.classes;
    let buttonClass = '';
    if (success) {
      buttonClass = classes.successButton;
    }

    return (
      <div className={classes.wrapper}>
        <Button fab color="primary" className={buttonClass} onClick={this.props.onClick}>
          {success ?  <Icon name="check" /> : <Icon name="save" />}
        </Button>
        {loading && <CircularProgress size={60} className={classes.progress} />}
      </div>
    );
  }
}

export default withStyles(styleSheet)(CircularFab);
