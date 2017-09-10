import { combineReducers } from 'redux';
import { reducer as form } from 'redux-form';
import table from 'admin/reducers/tableReducer';
import entity from 'admin/reducers/entityReducer';

const rootReducer = combineReducers({
  form,
  entity,
  table
});

export default rootReducer;
