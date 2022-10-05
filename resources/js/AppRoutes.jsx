import { BrowserRouter, Route, Routes } from "react-router-dom";
import CampaignOverview from "./routes/campaign/CampaignOverview";
import Connections from "./routes/connections/Connections";
import MainProfile from "./routes/dashboard/MainProfile";

// All React Routes are using the basename '/u' as included in the catch-all
// laravel route in frontend.php, when using components such as <Link>, there is
// no need to include the /u/ part. IE: <Link to='/followers> will correctly point
// to localhost/u/followers.

export default function AppRoutes() {
  return (
    <BrowserRouter basename="/u">
      <Routes>
        <Route path='/' element={<MainProfile key='dashboard' asDashboard />} />
        <Route path='/profile/:username' element={<MainProfile key='profile' />} />

        <Route path='/followers/:username' element={<Connections initialType='followers' />} />
        <Route path='/following/:username' element={<Connections initialType='following' />} />

        <Route path='/followers' element={<Connections initialType='followers' />} />
        <Route path='/following' element={<Connections initialType='following' />} />
        <Route path='/available' element={<Connections initialType='available' />} />

        <Route path='/campaign' element={<CampaignOverview />} />
      </Routes>
    </BrowserRouter>
  )
}

// /u/connections/followers/bitcoiners
// /u/connections/followers/shitcoiners
// /u/connections/all/bitcoiners
