import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { reduxForm } from 'redux-form';
import { entityLoad, entityUpdate } from 'admin/actions/entityActions';
import Loader from 'components/Loader';
import SubmitButton from './SubmitButton';

class EntityForm extends React.Component {
  static propTypes = {
    entityID:   PropTypes.oneOfType([PropTypes.string, PropTypes.number]).isRequired,
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
    const { handleSubmit, entity, submitting, reset, children } = this.props;

    if (entity.isLoading) {
      return <Loader />;
    }

    return (
      <form className="upa-form" onSubmit={handleSubmit(this.handleSubmit)}>
        <div className="card upa-card">
          <div className="card-content">
            <span className="card-title">
              Editing {entity.data.username}
            </span>
            {children}
          </div>
          <div className="card-action">
            <SubmitButton isSubmitting={submitting}>
              Update
            </SubmitButton>
            <button type="button" className="btn" onClick={reset} disabled={submitting}>
              Reset
            </button>
          </div>
        </div>
      </form>
    );
  }
}

function mapStateToProps(state) {
  return {
    entity:        assign({}, state.entity),
    initialValues: assign({}, state.entity.data)
  };
}

const formOptions = {
  form:               'entityForm',
  enableReinitialize: true
};

export default connect(mapStateToProps)(reduxForm(formOptions)(EntityForm));
