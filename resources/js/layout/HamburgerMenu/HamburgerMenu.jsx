import { useContext, useState } from 'react';
import * as Dialog from '@radix-ui/react-dialog';

import './HamburgerMenu.scss';
import { NavLink } from 'react-router-dom';
import ButtonWithLightning from '../Button/ButtonWithLightning';
import SatsIcon from '../../assets/icons/SatsIcon';
import { CompactNumberFormat } from '../../utils/NumberFormatting';
import BitcoinersNetworkLogoPlain from '../../assets/icons/BitcoinersNetworkLogoPlain';
import MatterMostIcon from '../../assets/icons/MatterMostIcon';
import TwitterIcon from '../../assets/icons/TwitterIcon';
import GithubIcon from '../../assets/icons/GithubIcon';
import TopUpModal from '../../components/MassConnectModal/TopUpModal';
import classNames from 'classnames';
import AppContext from '../../store/AppContext';

export default function HamburgerMenu({ variant }) {

  const [state] = useContext(AppContext);

  const [showTopup, setShowTopup] = useState(false);
  const [showMenu, setShowMenu] = useState(false);

  const onClickTopup = () => {
    setShowMenu(false);
    setShowTopup(true);
  }

  const logout = async () => {
    await axios.get('/frontend/logout');
    window.location.href = '/';
  }

  const { currentUser, availableSats } = state;

  const classes = classNames("__hamburger-menu", `__hamburger-menu-variant-${variant}`)

  const closeMenu = () => {
    setShowMenu(false);
  }

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
              <Dialog.Close asChild><div role="button" className='__modal-close-icon'>×</div></Dialog.Close>

              <div>
                <div className='link-group'>
                  <div><NavLink className="link" to='/dashboard'>Dashboard</NavLink></div>
                  <div><NavLink className="link" to={`/profile/${currentUser?.twitter_username}`} onClick={closeMenu}>View Public Profile</NavLink></div>
                </div>
                
                <div className='link-group'>
                  <div><NavLink className="link" to='/followers'>Your Followers</NavLink></div>
                  <div><NavLink className="link" to='/following'>Following</NavLink></div>
                  <div><NavLink className="link" to='/available'>Bitcoiners Network</NavLink></div>
                </div>
    
                <div className='link-group'>
                  <div><NavLink className="link" to='/campaign'>Follow Campaign</NavLink></div>
                  <div><NavLink className="link" to='/transactions'>Transaction History</NavLink></div>
                </div>

                <div className='link-group'>
                  <div className="link" role="button" onClick={logout}>Logout</div>
                </div>

              </div>

              <div>
                <div className='balance'>
                  <div className='label'>Balance</div>
                  <div>{ CompactNumberFormat(availableSats, { digits: 12 })}</div>
                  <SatsIcon />
                </div>

                <ButtonWithLightning onClick={onClickTopup} className="top-up">Top Up</ButtonWithLightning>
              </div>

              <div className='connect'>
                <div className='logo'><BitcoinersNetworkLogoPlain /></div>
                <a href="https://twitter.com/utxo_one" target="_blank" rel="noreferrer"><TwitterIcon className='social-icon' /></a>
                <a href="https://chat.utxo.one/" target="_blank" rel="noreferrer"><MatterMostIcon className='social-icon' /></a>
                <a href="https://github.com/utxo-one/bitcoiners-network/" target="_blank" rel="noreferrer"><GithubIcon className='social-icon' /></a>
              </div>
            </div>
          </Dialog.Content>
        </Dialog.Portal>
      </Dialog.Root>

      <TopUpModal show={showTopup} onHide={() => setShowTopup(false)} />
    </>
  )
}
