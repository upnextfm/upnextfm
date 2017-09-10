import React from 'react';
import { Switch, Route } from 'react-router-dom';
import Nav from 'admin/components/Nav';
import Dashboard from 'admin/routes/Dashboard';
import Users from 'admin/routes/Users';
import Rooms from 'admin/routes/Rooms';
import Playlists from 'admin/routes/Playlists';

const App = () => (
  <div>
    <Nav />
    <div className="container upa-container">
      <Switch>
        <Route exact path="/" component={Dashboard} />
        <Route path="/users" component={Users} />
        <Route path="/rooms" component={Rooms} />
        <Route path="/playlists" component={Playlists} />
      </Switch>
    </div>
  </div>
);

export default App;
