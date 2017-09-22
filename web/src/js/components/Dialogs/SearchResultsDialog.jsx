import React from 'react';
import PropTypes from 'prop-types';
import { CircularProgress } from 'material-ui/Progress';
import { Scrollbars } from 'react-custom-scrollbars';
import Dialog, { DialogActions, DialogContent, DialogContentText } from 'material-ui/Dialog';
import Card, { CardContent, CardMedia } from 'material-ui/Card';
import Typography from 'material-ui/Typography';
import Slide from 'material-ui/transitions/Slide';
import Grid from 'material-ui/Grid';
import Button from 'components/Button';
import Icon from 'components/Icon';

export default class SearchResultsDialog extends React.PureComponent {
  static propTypes = {
    isOpen:        PropTypes.bool,
    searchTerm:    PropTypes.string,
    searchResults: PropTypes.array,
    isSubmitting:  PropTypes.bool,
    error:         PropTypes.node,
    onSubmit:      PropTypes.func,
    onChange:      PropTypes.func,
    onClose:       PropTypes.func,
    onClickAdd:    PropTypes.func
  };

  static defaultProps = {
    error:         null,
    isOpen:        false,
    isSubmitting:  false,
    searchResults: [],
    onSubmit:      () => {},
    onChange:      () => {},
    onClose:       () => {},
    onClickAdd:    () => {}
  };

  handleKeyDown = (e) => {
    if (e.keyCode === 13) {
      this.props.onSubmit(e.target.value);
    }
  };

  renderSearchForm() {
    return (
      <div className="up-dialog-search-results__form">
        <input
          id="name"
          className="up-form-control"
          value={this.props.searchTerm}
          onChange={this.props.onChange}
          onKeyDown={this.handleKeyDown}
        />
      </div>
    );
  }

  renderSearchGrid() {
    return (
      <Scrollbars className="up-dialog-search-results__scroll">
        <Grid className="up-dialog-search-results__grid" container>
          {this.props.searchResults.map((item) => {
            if (!item.id.videoId) {
              return null;
            }

            return (
              <Grid key={item.id.videoId} item xs={12} lg={2}>
                <Card className="up-card up-card--search-results">
                  <CardMedia className="up-card__media" image={item.snippet.thumbnails.medium.url}>
                    <Button
                      color="primary"
                      onClick={() => {
                        this.props.onClickAdd(item);
                      }}
                      fab
                    >
                      <Icon name="add" />
                    </Button>
                  </CardMedia>
                  <CardContent className="up-card__content">
                    <Typography type="headline" component="h2">
                      {item.snippet.title}
                    </Typography>
                  </CardContent>
                </Card>
              </Grid>
            );
          })}
        </Grid>
      </Scrollbars>
    );
  }

  render() {
    const { isOpen } = this.props;

    return (
      <Dialog
        open={isOpen}
        transition={Slide}
        className="up-dialog--wide"
        onRequestClose={this.props.onClose}
      >
        <DialogContent className="up-dialog-search-results__content">
          <div className="up-dialog-search-results">
            {this.renderSearchForm()}
            {this.renderSearchGrid()}
          </div>
        </DialogContent>
      </Dialog>
    );
  }
}
