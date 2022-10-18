import React from 'react';
import ReactDOM from 'react-dom/client';
import { enableMapSet } from "immer"
import AppAuth from './AppAuth';

import './app.css';
import './layout/Modal/Modal.scss';
import './layout/DropdownMenu/DropdownMenu.scss';

// Enable using immer for Map and Set
enableMapSet();

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <AppAuth />
  </React.StrictMode>
)
