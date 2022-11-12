import { createContext } from "react";

const AppContext = createContext();

export const APP_INITIAL_STATE = {
  availableSats    : null,
  currentUser      : null,
  rates            : null,
  metrics          : {},
  publicUser       : false,
  requestsLoaded   : false,
  endorsementTypes : {},
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

    case 'currentUser/set-follow-data':
      draft.currentUser.follower_data = action.payload.follower_data;
      draft.currentUser.following_data = action.payload.following_data;
      break;

    case 'currentUser/close-vote-tip':
      draft.currentUser.closed_vote_tip = true;
      break;

    case 'rates/set':
      draft.rates = action.payload;
      break;

    case 'metrics/set-bitcoiners':
      draft.metrics.bitcoiners = action.payload;
      break;

    case 'publicUser/set':
      draft.publicUser = action.payload;

    case 'requestsLoaded/set':
      draft.requestsLoaded = action.payload;

    case 'endorsementTypes/set':
      draft.endorsementTypes = action.payload;
  }
}


export default AppContext;
