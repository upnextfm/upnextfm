import { combineReducers } from 'redux';
import { reducer as form } from 'redux-form';
import table from 'admin/reducers/tableReducer';

const rootReducer = combineReducers({
  form,
  table
});

export default rootReducer;
