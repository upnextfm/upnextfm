import React from 'react';
import { connect } from 'react-redux';

class Rooms extends React.Component {
  render() {
    const { table } = this.props;

    return (
      <div>Rooms</div>
    );
  }
}

function mapStateToProps(state) {
  return {
    table: Object.assign({}, state.table)
  };
}

export default connect(mapStateToProps)(Rooms);
