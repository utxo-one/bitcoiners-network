import React from 'react';
import ReactDOM from 'react-dom/client';
import AppRoutes from './AppRoutes';

import './app.css';
import './palette.scss';
import './layout/Modal/Modal.scss';
import './layout/DropdownMenu/DropdownMenu.scss';

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <AppRoutes /> 
  </React.StrictMode>
)
