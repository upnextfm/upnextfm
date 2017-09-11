import React from 'react';
import { Switch, Route } from 'react-router-dom';
import EntityIndex from 'admin/components/EntityIndex';
import UsersEdit from './Users/UsersEdit';

const Users = () => (
  <Switch>
    <Route exact path="/users/:page?">
      <EntityIndex entityName="users" />
    </Route>
    <Route exact path="/users/edit/:id" component={UsersEdit} />
  </Switch>
);

export default Users;
