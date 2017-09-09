import React from 'react';
import { connect } from 'react-redux';
import { tableLoad } from 'admin/actions/tableActions';
import Table from 'admin/components/Table';

class Users extends React.Component {
  componentDidMount() {
    this.props.dispatch(tableLoad('users'));
  }

  handleClickRow = (e, row) => {
    console.info(row);
  };

  handleSubmitFilter = (e, value) => {
    console.info(value);
  };

  render() {
    return (
      <div>
        <Table
          onClickRow={this.handleClickRow}
          onSubmitFilter={this.handleSubmitFilter}
        />
      </div>
    );
  }
}

function mapStateToProps(state) {
  return {
    table: assign({}, state.table)
  };
}

export default connect(mapStateToProps)(Users);
