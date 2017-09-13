import React from 'react';
import { Switch, Route } from 'react-router-dom';
import EntityIndex from 'admin/components/EntityIndex';
import RoomsEdit from './Rooms/RoomsEdit';

const Rooms = () => (
  <Switch>
    <Route exact path="/rooms/:page?">
      <EntityIndex entityName="room" />
    </Route>
    <Route exact path="/room/edit/:id" component={RoomsEdit} />
  </Switch>
);

export default Rooms;
