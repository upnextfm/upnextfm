import React from 'react';
import { connect } from 'react-redux';
import { EntityForm, InputField, ThumbField } from 'admin/forms';
import { Field } from 'redux-form';

const RoomsEdit = ({ entity, ...props }) => {
  return (
    <EntityForm entityName="room" entityID={props.match.params.id}>
      <Field name="thumb" entity={entity} component={ThumbField} />
      <Field type="text" name="name" label="Name" component={InputField} />
      <Field type="text" name="displayName" label="Display Name" component={InputField} />
      <Field type="textarea" name="description" label="Description" component={InputField} />
      <Field type="checkbox" name="isPrivate" label="Private" component={InputField} />
      <Field type="checkbox" name="isDeleted" label="Deleted" component={InputField} />
    </EntityForm>
  );
};

function mapStateToProps(state) {
  return {
    entity: assign({}, state.entity)
  };
}

export default connect(mapStateToProps)(RoomsEdit);

