import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { objectKeyFilter } from 'utils/objects';
import { CircularProgress } from 'material-ui/Progress';
import { Scrollbars } from 'react-custom-scrollbars';
import Dialog, { DialogActions, DialogContent, DialogContentText } from 'material-ui/Dialog';
import Card, { CardActions, CardContent, CardMedia } from 'material-ui/Card';
import Typography from 'material-ui/Typography';
import Slide from 'material-ui/transitions/Slide';
import Button from 'material-ui/Button';
import AddIcon from 'material-ui-icons/Add';
import Grid from 'material-ui/Grid';

export default class SearchResultsDialog extends Component {
  static propTypes = {
    isOpen:        PropTypes.bool,
    searchResults: PropTypes.array,
    isSubmitting:  PropTypes.bool,
    error:         PropTypes.node,
    onClose:       PropTypes.func,
    onClickAdd:    PropTypes.func
  };

  static defaultProps = {
    error:         null,
    isOpen:        false,
    isSubmitting:  false,
    searchResults: [],
    onClose:       () => {},
    onClickAdd:    () => {}
  };

  renderSearchResults() {
    return (
      <Grid container>
        {this.props.searchResults.map((item) => {
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
                    <AddIcon />
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
    );
  }

  render() {
    const { isOpen, isSubmitting, error, ...props } = this.props;

    return (
      <Dialog
        open={isOpen}
        transition={Slide}
        onRequestClose={this.props.onClose}
        className="up-dialog--wide"
        {...objectKeyFilter(props, SearchResultsDialog.propTypes)}
      >
        <Scrollbars>
          <DialogContent>
            {error && (
              <DialogContentText className="up-error">
                {error.message}
              </DialogContentText>
            )}
            {this.renderSearchResults()}
          </DialogContent>
        </Scrollbars>
      </Dialog>
    );
  }
}
