import React from 'react';
import { EntityForm, InputField } from 'admin/forms';
import { Field } from 'redux-form';

const UsersEdit = props => (
  <EntityForm entityName="user" entityID={props.match.params.id}>
    <Field type="text" name="username" label="Username" component={InputField} />
    <Field type="email" name="email" label="Email" component={InputField} />
    <Field type="text" name="newPassword" label="New Password" component={InputField} />
    <Field type="text" name="info.location" label="Location" component={InputField} />
    <Field type="text" name="info.website" label="Website" component={InputField} />
    <Field type="textarea" name="info.bio" label="Bio" component={InputField} />
    <Field type="checkbox" name="enabled" label="Account Enabled" component={InputField} />
  </EntityForm>
);

export default UsersEdit;

