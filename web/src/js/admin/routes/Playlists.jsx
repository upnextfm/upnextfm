import React from 'react';
import { connect } from 'react-redux';

class Playlists extends React.Component {
  render() {
    const { table } = this.props;

    return (
      <div>Playlists</div>
    );
  }
}

function mapStateToProps(state) {
  return {
    table: Object.assign({}, state.table)
  };
}

export default connect(mapStateToProps)(Playlists);
