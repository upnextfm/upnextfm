import React from 'react';
import { connect } from 'react-redux';
import { tableLoad } from 'admin/actions/tableActions';
import Table from 'admin/components/Table';

class Index extends React.Component {
  componentDidMount() {
    this.props.dispatch(tableLoad('users', 1));
  }

  handleChangePage = (e, page) => {
    this.props.dispatch(tableLoad('users', page));
  };

  handleSubmitFilter = (e, value) => {
    console.info(value);
  };

  handleClickRow = (e, row) => {
    this.props.history.push(`/users/${row.id}`);
  };

  render() {
    return (
      <Table
        onClickRow={this.handleClickRow}
        onChangePage={this.handleChangePage}
        onSubmitFilter={this.handleSubmitFilter}
      />
    );
  }
}

function mapStateToProps(state) {
  return {
    table: assign({}, state.table)
  };
}

export default connect(mapStateToProps)(Index);
