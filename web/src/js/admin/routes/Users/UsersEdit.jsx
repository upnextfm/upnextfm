import React from 'react';
import { connect } from 'react-redux';
import { EntityForm, InputField, AvatarField } from 'admin/forms';
import { Field } from 'redux-form';

const UsersEdit = ({ entity, ...props }) => {
  return (
    <EntityForm entityName="user" entityID={props.match.params.id}>
      <Field name="avatar" entity={entity} component={AvatarField} />
      <Field type="text" name="username" label="Username" component={InputField} />
      <Field type="email" name="email" label="Email" component={InputField} />
      <Field type="text" name="newPassword" label="New Password" component={InputField} />
      <Field type="text" name="info.location" label="Location" component={InputField} />
      <Field type="text" name="info.website" label="Website" component={InputField} />
      <Field type="textarea" name="info.bio" label="Bio" component={InputField} />
      <Field type="checkbox" name="enabled" label="Account Enabled" component={InputField} />
    </EntityForm>
  );
};

function mapStateToProps(state) {
  return {
    entity: assign({}, state.entity)
  };
}

export default connect(mapStateToProps)(UsersEdit);

