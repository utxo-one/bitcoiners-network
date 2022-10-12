import { BrowserRouter, Navigate, Outlet, Route, Routes } from "react-router-dom";
import { useImmerReducer } from "use-immer";
import produce from "immer";
import CampaignOverview from "./routes/campaign/CampaignOverview";
import Connections from "./routes/connections/Connections";
import MainProfile from "./routes/dashboard/MainProfile";
import TransactionsOverview from "./routes/transactions/TransactionsOverview";
import AppContext from "./store/AppContext";
import AuthRoute from "./routes/AuthRoute";
import ScrollToTop from "./components/ScrollToTup";

// All React Routes are using the basename '/u' as included in the catch-all
// laravel route in frontend.php, when using components such as <Link>, there is
// no need to include the /u/ part. IE: <Link to='/followers> will correctly point
// to localhost/u/followers.

const initialState = {
  availableSats : 0,
  currentUser   : null,
  rates         : null,
}

const appReducer = produce((draft, action) => {
  switch (action.type) {
    case 'SET_BALANCE':
      draft.availableSats = action.value;
      break;
  }
});

const Out = () => (
  <>
  <div>OUT</div>
  <Outlet />
  </>
)
export default function AppRoutes() {

  const [state, dispatch] = useImmerReducer(appReducer, initialState);

  return (
      <BrowserRouter basename="/u">
        <ScrollToTop />
        <Routes>
          <Route path='/dashboard' element={<MainProfile key='dashboard' asDashboard />} />
          <Route path='/profile/:username' element={<MainProfile key='profile' />} />

          <Route path='/p' element={<Out />}>
            <Route path='property' element={<div>PROPERTY!</div>} />
            <Route path='*' element={<div>ROOT</div>} />
          </Route>

          <Route path='/test' element={<AuthRoute element={<Connections initialType='followers' />} />} />

          <Route path='/followers/:username' element={<Connections initialType='followers' />} />
          <Route path='/following/:username' element={<Connections initialType='following' />} />

          <Route path='/followers' element={<Connections initialType='followers' key='followers' />} />
          <Route path='/following' element={<Connections initialType='following' key='following' />} />
          <Route path='/available' element={<Connections initialType='available' key='available' />} />

          <Route path='/campaign' element={<CampaignOverview />} />
          <Route path='/transactions' element={<TransactionsOverview />} />
          
          <Route path='*' element={<Navigate replace to="/dashboard" />} />
        </Routes>
      </BrowserRouter>
  )
}
