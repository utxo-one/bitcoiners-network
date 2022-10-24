import { BrowserRouter, Navigate, Outlet, Route, Routes } from "react-router-dom";
import CampaignOverview from "./routes/campaign/CampaignOverview";
import Connections from "./routes/connections/Connections";
import MainProfile from "./routes/dashboard/MainProfile";
import TransactionsOverview from "./routes/transactions/TransactionsOverview";
import AuthRoute from "./routes/AuthRoute";
import ScrollToTop from "./components/ScrollToTup";

// All React Routes are using the basename '/u' as included in the catch-all
// laravel route in frontend.php, when using components such as <Link>, there is
// no need to include the /u/ part. IE: <Link to='/followers> will correctly point
// to localhost/u/followers.
export default function AppRoutes() {

  return (
      <BrowserRouter basename="/u">
        <ScrollToTop />
        <Routes>
          <Route path='/profile/:username' element={<MainProfile key='profile' />} />

          <Route path='/dashboard' element={<AuthRoute element={<MainProfile key='dashboard' asDashboard />} />} />

          <Route path='/followers/:username' element={<AuthRoute element={<Connections initialType='followers' />} />} />
          <Route path='/following/:username' element={<AuthRoute element={<Connections initialType='following' />} />} />

          <Route path='/followers' element={<AuthRoute element={<Connections initialType='followers' key='followers' />} />} />
          <Route path='/following' element={<AuthRoute element={<Connections initialType='following' key='following' />} />} />
          <Route path='/available' element={<AuthRoute element={<Connections initialType='available' key='available' />} />} />

          <Route path='/campaign' element={<AuthRoute element={<CampaignOverview />} />} />
          <Route path='/transactions' element={<AuthRoute element={<TransactionsOverview />} />} />
          
          <Route path='*' element={<Navigate replace to="/dashboard" />} />
        </Routes>
      </BrowserRouter>
  )
}
