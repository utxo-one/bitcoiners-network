import { useState } from 'react';
import * as Dialog from '@radix-ui/react-dialog';

import './HamburgerMenu.scss';
import { Link, NavLink } from 'react-router-dom';
import ButtonWithLightning from '../Button/ButtonWithLightning';
import SatsIcon from '../../assets/icons/SatsIcon';
import { CompactNumberFormat } from '../../utils/NumberFormatting';
import BitcoinersNetworkLogoPlain from '../../assets/icons/BitcoinersNetworkLogoPlain';
import MatterMostIcon from '../../assets/icons/MattermostIcon';
import TwitterIcon from '../../assets/icons/TwitterIcon';
import GithubIcon from '../../assets/icons/GithubIcon';
import TopUpModal from '../../components/MassConnectModal/TopUpModal';
import classNames from 'classnames';
import NavigationLink from '../NavigationLink';

export default function HamburgerMenu({ availableSats = 135123, variant }) {

  const [showTopup, setShowTopup] = useState(false);
  const [showMenu, setShowMenu] = useState(false);

  const onClickTopup = () => {
    setShowMenu(false);
    setShowTopup(true);
  }

  const classes = classNames("__hamburger-menu", `__hamburger-menu-variant-${variant}`)

  return (   
    <> 
      <div role="button" className={classes} onClick={() => setShowMenu(true)}>
        <div className="__hamburger-menu-line" />
        <div className="__hamburger-menu-line" />
        <div className="__hamburger-menu-line" />
      </div>
      
      <Dialog.Root open={showMenu} onOpenChange={() => setShowMenu(false)}>
        <Dialog.Portal>
          <Dialog.Overlay className="__dialog-overlay" />
          <Dialog.Content className="__modal __hamburger-menu-modal">

            <div className='content'>
              <div className='balance'>
                <div className='label'>Balance</div>
                <div>{ CompactNumberFormat(availableSats, { digits: 12 })}</div>
                <SatsIcon />
              </div>

              <div>
                <div className='link-group'>
                  <div><NavLink to='/dashboard'>Dashboard</NavLink></div>
                </div>
                
                <div className='link-group'>
                  <div><NavLink to='/followers'>Your Followers</NavLink></div>
                  <div><NavLink to='/following'>Following</NavLink></div>
                  <div><NavLink to='/available'>Bitcoiners Network</NavLink></div>
                </div>
    
                <div className='link-group'>
                  <div><NavLink to='/campaign'>Mass Follow Campaign</NavLink></div>
                  <div><NavLink to='/transactions'>Transaction History</NavLink></div>
                </div>
              </div>

              <ButtonWithLightning onClick={onClickTopup} className="top-up">Top Up</ButtonWithLightning>

              <div className='connect'>
                <div className='logo'><BitcoinersNetworkLogoPlain /></div>
                <TwitterIcon className='social-icon' />
                <MatterMostIcon className='social-icon' />
                <GithubIcon className='social-icon' />
              </div>
            </div>
          </Dialog.Content>
        </Dialog.Portal>
      </Dialog.Root>

      <TopUpModal show={showTopup} onHide={() => setShowTopup(false)} />
    </>
  )
}
