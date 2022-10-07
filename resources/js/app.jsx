import React from 'react';
import ReactDOM from 'react-dom/client';
import AppRoutes from './AppRoutes';
import { enableMapSet } from "immer"

import './app.css';
import './palette.scss';
import './layout/Modal/Modal.scss';
import './layout/DropdownMenu/DropdownMenu.scss';

enableMapSet();

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <AppRoutes /> 
  </React.StrictMode>
)
