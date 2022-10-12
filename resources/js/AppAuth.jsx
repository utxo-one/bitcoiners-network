import { useImmerReducer } from "use-immer";
import AppRoutes from "./AppRoutes";
import axios from "axios";
import { useEffect } from "react";
import AppContext, { appReducer, APP_INITIAL_STATE } from "./store/AppContext";

export default function AppAuth() {

  const [state, dispatch] = useImmerReducer(appReducer, APP_INITIAL_STATE);

  useEffect(() => {

    const initialLoad = async () => {
      const { data: balance } = await axios.get('/frontend/user/available-balance');
      const { data: currentUser } = await axios.get('/frontend/user/auth');
      const { data: rates } = await axios.get('/frontend/rates');
      
      dispatch({ type: 'balance/set', payload: balance });
      dispatch({ type: 'currentUser/set', payload: currentUser });
      dispatch({ type: 'rates/set', payload: rates });
    }

    initialLoad();
  }, []);

  return  (
    <AppContext.Provider value={[state, dispatch]}>
      <AppRoutes />
    </AppContext.Provider>
  );
}
