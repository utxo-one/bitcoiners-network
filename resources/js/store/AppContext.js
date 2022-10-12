import { createContext } from "react";

const AppContext = createContext();

export const APP_INITIAL_STATE = {
  availableSats   : 0,
  currentUser     : null,
  rates           : null,
  metrics         : {},
}

export const appReducer = (draft, action) => {
  switch (action.type) {
    case 'balance/set':
      draft.availableSats = action.payload;
      break;

    case 'balance/spend':
      draft.availableSats -= action.payload;
      break;

    case 'currentUser/set':
      draft.currentUser = action.payload;
      break;

    case 'rates/set':
      draft.rates = action.payload;
      break;

    case 'metrics/set-bitcoiners':
      draft.metrics.bitcoiners = action.payload;
      break;
  }
}


export default AppContext;
