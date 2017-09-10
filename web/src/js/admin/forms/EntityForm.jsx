import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { reduxForm } from 'redux-form';
import { entityLoad, entityUpdate } from 'admin/actions/entityActions';
import Card from 'admin/components/Card';
import Loader from 'components/Loader';
import SubmitButton from './SubmitButton';

class EntityForm extends React.Component {
  static propTypes = {
    entityID: PropTypes.oneOfType([
      PropTypes.string,
      PropTypes.number
    ]).isRequired,
    entityName: PropTypes.string.isRequired
  };

  componentDidMount() {
    this.props.dispatch(
      entityLoad(this.props.entityName, this.props.entityID)
    );
  }

  componentDidUpdate() {
    if (Materialize.updateTextFields) {
      Materialize.updateTextFields();
    }
  }

  handleSubmit = (values) => {
    return this.props.dispatch(
      entityUpdate(this.props.entityName, this.props.entityID, values)
    );
  };

  render() {
    const { handleSubmit, entity, ui, submitting, reset, children } = this.props;

    if (ui.isLoading) {
      return <Loader />;
    }

    const actions = [
      <SubmitButton key="submit" isSubmitting={submitting}>
        Update
      </SubmitButton>,
      <button key="reset" type="button" className="btn" onClick={reset} disabled={submitting}>
        Reset
      </button>
    ];

    return (
      <form className="upa-form" onSubmit={handleSubmit(this.handleSubmit)}>
        <Card title={`Editing ${entity.data.username}`} actions={actions}>
          {children}
        </Card>
      </form>
    );
  }
}

function mapStateToProps(state) {
  return {
    ui:            assign({}, state.ui),
    entity:        assign({}, state.entity),
    initialValues: assign({}, state.entity.data)
  };
}

const formOptions = {
  form:               'entityForm',
  enableReinitialize: true
};

export default connect(mapStateToProps)(reduxForm(formOptions)(EntityForm));
