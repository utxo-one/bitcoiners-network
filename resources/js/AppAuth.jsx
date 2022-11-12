import { useImmerReducer } from "use-immer";
import AppRoutes from "./AppRoutes";
import axios from "axios";
import { useEffect } from "react";
import AppContext, { appReducer, APP_INITIAL_STATE } from "./store/AppContext";
import useEndorsements from "./hooks/useEndorsements";

export default function AppAuth() {

  const [state, dispatch] = useImmerReducer(appReducer, APP_INITIAL_STATE);
  const { loadEndorsements } = useEndorsements();

  useEffect(() => {

    const initialLoad = async () => {
      // always set endorsement types for now
      try {
        const { data: endorsementTypes } = await axios.get('/frontend/endorsement-types');
        dispatch({ type: 'endorsementTypes/set', payload: endorsementTypes });
      }
      catch {
        console.log("couldn't load endorsements");
      }

      // TODO -> set error correctly (auth fails with { message: 'unauthorized' })
      try {
        const { data: currentUser } = await axios.get('/frontend/current-user/auth');
        const { data: balance } = await axios.get('/frontend/current-user/available-balance');
        const { data: rates } = await axios.get('/frontend/rates');

        const { endorsements } = await loadEndorsements(currentUser.twitter_username);

        if (endorsements) {
          currentUser._endorsements = endorsements.endorsement_data;
        }

        dispatch({ type: 'balance/set', payload: balance });
        dispatch({ type: 'currentUser/set', payload: currentUser });
        dispatch({ type: 'rates/set', payload: rates });
        dispatch({ type: 'requestsLoaded/set', payload: true });
      }
      catch {
        dispatch({ type: 'publicUser/set', payload: true });
      }
    }

    initialLoad();
  }, []);

  return  (
    <AppContext.Provider value={[state, dispatch]}>
      <AppRoutes />
    </AppContext.Provider>
  );
}
