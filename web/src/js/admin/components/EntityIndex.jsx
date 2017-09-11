import React from 'react';
import PropTypes from 'prop-types';
import queryString from 'query-string';
import { withRouter } from 'react-router';
import { tableLoad } from 'admin/actions/tableActions';
import Table from 'admin/components/Table';

class EntityIndex extends React.Component {
  static contextTypes = {
    store: PropTypes.object.isRequired
  };

  static propTypes = {
    entityName: PropTypes.string.isRequired
  };

  /**
   * @param {*} props
   * @param {*} context
   */
  constructor(props, context) {
    super(props, context);
    this.dispatch = context.store.dispatch;
  }

  /**
   *
   */
  componentDidMount() {
    this.dispatch(
      tableLoad(this.props.entityName, this.page(), this.filter())
    );
  }

  /**
   * @param {*} prevProps
   */
  componentDidUpdate(prevProps) {
    if (prevProps.location !== this.props.location) {
      this.dispatch(
        tableLoad(this.props.entityName, this.page(), this.filter())
      );
    }
  }

  /**
   * @returns {number}
   */
  page = () => {
    let page = this.props.match.params.page || 1;
    if (page < 1) {
      page = 1;
    }
    return page;
  };

  /**
   * @returns {string}
   */
  filter = () => {
    const query = queryString.parse(this.props.location.search);
    return query.filter || '';
  };

  /**
   * @param {Event} e
   * @param {string} value
   */
  handleSubmitFilter = (e, value) => {
    this.props.history.push(
      `/${this.props.entityName}?filter=${encodeURIComponent(value)}`
    );
  };

  /**
   * @param {Event} e
   * @param {number} page
   */
  handleChangePage = (e, page) => {
    this.props.history.push(
      `/${this.props.entityName}/${page}`
    );
  };

  /**
   * @param {Event} e
   * @param {*} row
   */
  handleClickRow = (e, row) => {
    this.props.history.push(
      `/${this.props.entityName}/edit/${row.id}`
    );
  };

  /**
   * @returns {XML}
   */
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

export default withRouter(EntityIndex);
