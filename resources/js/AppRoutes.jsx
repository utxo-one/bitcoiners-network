import { BrowserRouter, Route, Routes } from "react-router-dom";
import Connections from "./routes/connections/Connections";
import Dashboard from "./routes/dashboard/Dashboard";

export default function AppRoutes() {
  return (
    <BrowserRouter basename="/u">
      <Routes>
        <Route path='/' element={<Dashboard />} />
        <Route path='/followers' element={<Connections type='followers' />} />
        <Route path='/following' element={<Connections type='following' />} />
      </Routes>
    </BrowserRouter>
  )
}