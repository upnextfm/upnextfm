import React from 'react';
import { connect } from 'react-redux';
import { reduxForm } from 'redux-form';
import { entityLoad, entityUpdate } from 'admin/actions/entityActions';
import Loader from 'components/Loader';
import InputField from 'admin/components/InputField';
import SubmitButton from 'admin/components/SubmitButton';

class Edit extends React.Component {
  constructor(props) {
    super(props);
    this.entityID = this.props.match.params.id;
  }

  componentDidMount() {
    this.props.dispatch(entityLoad('user', this.entityID));
  }

  componentDidUpdate() {
    if (Materialize.updateTextFields) {
      Materialize.updateTextFields();
    }
  }

  handleSubmit = (values) => {
    return this.props.dispatch(entityUpdate('user', this.entityID, values));
  };

  render() {
    const { handleSubmit, submitting, entity } = this.props;

    if (entity.isLoading) {
      return <Loader />;
    }

    return (
      <form onSubmit={handleSubmit(this.handleSubmit)}>
        <InputField name="username" label="Username" />
        <InputField name="email" label="Email" />
        <SubmitButton isSubmitting={submitting}>
          Update
        </SubmitButton>
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
  form:               'userForm',
  enableReinitialize: true
};

export default connect(mapStateToProps)(reduxForm(formOptions)(Edit));

