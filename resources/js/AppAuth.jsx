import { useImmerReducer } from "use-immer";
import AppRoutes from "./AppRoutes";
import axios from "axios";
import { useEffect } from "react";
import AppContext, { appReducer, APP_INITIAL_STATE } from "./store/AppContext";

export default function AppAuth() {

  const [state, dispatch] = useImmerReducer(appReducer, APP_INITIAL_STATE);

  useEffect(() => {

    const initialLoad = async () => {
      // TODO -> set error correctly (auth fails with { message: 'unauthorized' })
      try {
        const { data: currentUser } = await axios.get('/frontend/current-user/auth');
        const { data: balance } = await axios.get('/frontend/current-user/available-balance');
        const { data: rates } = await axios.get('/frontend/rates');

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
