import { combineReducers } from 'redux';
import { reducer as form } from 'redux-form';
import ui from './uiReducer';
import table from './tableReducer';
import entity from './entityReducer';

const rootReducer = combineReducers({
  form,
  ui,
  entity,
  table
});

export default rootReducer;
