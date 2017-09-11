import React from 'react';
import { connect } from 'react-redux';
import queryString from 'query-string';
import { tableLoad } from 'admin/actions/tableActions';
import Table from 'admin/components/Table';

class Index extends React.Component {
  componentDidMount() {
    const query = queryString.parse(this.props.location.search);
    this.props.dispatch(tableLoad('users', this.page(), query.filter || ''));
  }

  componentDidUpdate(prevProps) {
    if (prevProps.location !== this.props.location) {
      const query = queryString.parse(this.props.location.search);
      this.props.dispatch(tableLoad('users', this.page(), query.filter || ''));
    }
  }

  page = () => {
    let page = this.props.match.params.page || 1;
    if (page < 1) {
      page = 1;
    }

    return page;
  };

  handleSubmitFilter = (e, value) => {
    this.props.history.push(`/users?filter=${encodeURIComponent(value)}`);
  };

  handleChangePage = (e, page) => {
    this.props.history.push(`/users/${page}`);
  };

  handleClickRow = (e, row) => {
    this.props.history.push(`/users/edit/${row.id}`);
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

export default connect()(Index);
