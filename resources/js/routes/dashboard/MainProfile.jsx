import { useContext, useEffect, useRef, useState } from "react";
import { useImmer } from "use-immer";
import axios from "axios";
import { Link, useParams } from "react-router-dom";
import classNames from "classnames";
import { CompactNumberFormat } from "../../utils/NumberFormatting";

import AppContext from "../../store/AppContext";

import Box from "../../layout/Box/Box";
import ConnectionsBox from "./ConnectionsBox";
import ButtonWithLightning from "../../layout/Button/ButtonWithLightning";
import TwitterButton from "../../layout/Button/TwitterButton";
import Button from "../../layout/Button/Button";
import ConnectButton from "../../layout/Button/ConnectButton";
import HamburgerMenu from "../../layout/HamburgerMenu/HamburgerMenu";

import MassConnectModal from "../../components/MassConnectModal/MassConnectModal";
import ProfilePicture from "../../components/ProfilePicture/ProfilePicture";
import UserTypeBadge from "../../components/UserTypeBadge/UserTypeBadge";
import ConnectionTypeBadge from "../../components/ConnectionTypeBadge/ConnectionTypeBadge";
import CommunityRateModal from "../connections/CommunityRateModal";
import CampaignStats from "../../components/CampaignStats/CampaignStats";
import TopUpModal from "../../components/MassConnectModal/TopUpModal";

import SocialNetworkIcon from "../../assets/icons/SocialNetworkIcon";
import SatsIcon from "../../assets/icons/SatsIcon";
import BoltIcon from "../../assets/icons/BoltIcon";

import './MainProfile.scss';
import SignUpModal from "./SignUpModal";
import StarIcon from "../../assets/icons/StarIcon";
import EndorseIcon from "../../assets/icons/EndorseIcon";


export default function MainProfile({ asDashboard }) {
  const [state, dispatch] = useContext(AppContext);

  const { username } = useParams();

  const [loadedUser, setLoadedUser] = useImmer(null);
  const [campaignData, setCampaignData] = useState(null);
  const [showMassConnect, setShowMassConnect] = useState(false);
  const [handleVisible, setHandleVisible] = useState(false);
  const [showRate, setShowRate] = useState(false);
  const [showTopUp, setShowTopUp] = useState(false);
  const [showSignup, setShowSignup] = useState(false);
  const [initialLoad, setInitialLoad] = useState(false);
  
  const profilePicRef = useRef();
  const handleIntersector = useRef();
  
  const { currentUser, metrics, availableSats, publicUser } = state;

  const userData = asDashboard ? currentUser : loadedUser;
  
  useEffect(() => {
    const loadUserData = async () => {
      if (!asDashboard) {
        const { data } = await axios.get(`/frontend/user/${username}`);
        setLoadedUser(data);
      }
      
      else {
        const { data: campaignData } = await axios.get('/frontend/follow/mass-follow');
        setCampaignData(campaignData);

        // If metrics are not loaded:
        if (typeof metrics.bitcoiners !== 'number') {
          const { data: metrics } = await axios.get('/frontend/metrics/total-bitcoiners');
          dispatch({ type: 'metrics/set-bitcoiners', payload: metrics.totalBitcoiners });
        }
      }

      setInitialLoad(true);
    }

    loadUserData();
  }, []);

  useEffect(() => {
    const element = profilePicRef.current;

    if (handleIntersector.current) {
      handleIntersector.current.unobserve(element);
    }

    const handleIntersect = ([entry]) => {
      setHandleVisible(entry.isIntersecting);
    }

    handleIntersector.current = new IntersectionObserver(handleIntersect, { threshold: [0] });
    handleIntersector.current.observe(element);
  });

  const onClickBadge = () => {
    if (publicUser) {
      setShowSignup(true);
      return;
    }

    setShowRate(true);
  }

  const onToggleConnect = () => {
    if (publicUser) {
      setShowSignup(true);
      return;
    }

    setLoadedUser(draft => {
      draft.is_followed_by_authenticated_user = !draft.is_followed_by_authenticated_user;
    });
  }

  const onClickSignup = () => {
    window.location.href = "/auth/twitter";
  }

  // TODO -> change conditional
  const checkSignedUp = e => {
    if (publicUser) {
      e.stopPropagation();
      setShowSignup(true);
    }
  }

  const campaignRunning = campaignData?.status === 'running';

  const renderNetworkStats = () => (
    <Box className="bitcoiners-you-follow">
      <div className="data">
        <div className="label">Bitcoiners you follow</div>
        <div className="value">{ Number(userData?.following_data.bitcoiners).toLocaleString() }</div>
      </div>
      
      <div className="data">
        <div className="label">
          <span className="bitcoiners">bitcoiners</span><span className="network">.network</span> Pool
        </div>
        <div className="value pool">{ Number(metrics.bitcoiners).toLocaleString() }</div>
      </div>

      <hr />

      <div className="network-info">
        <SocialNetworkIcon />
        <div className="info">Our <Link to='/available' className="bitcoin-twitter">Bitcoin Twitter</Link> Network is operational and scanning users.</div>
      </div>

      { !campaignRunning && (
        <>
          <hr />

          <ButtonWithLightning className="mass-follow" onClick={() => setShowMassConnect(true)}>
            <div>Follow Bitcoiners</div>
          </ButtonWithLightning>
        </>
      )}
    </Box>
  )

  const renderCampaignStats = () => (
    <div className="mass-follow">
      <h3>Current Follow Campaign</h3>
      <Box>
        <CampaignStats campaign={campaignData} />
        <div className="view-campaign">
          <Button as={Link} to='/campaign' variant='outline'>View Campaign</Button>
        </div>
      </Box>
    </div>
  )

  const renderSatsCounter = () => {
    if (availableSats > 0) {
      return (
        <Link to='/transactions' className="link-transactions">
          <div className="sats-badge">
            { CompactNumberFormat(availableSats, { digits: 12 })}
            <SatsIcon />
          </div>
        </Link>
      )
    }

    return (
      <div className="top-up" role="button" onClick={() => setShowTopUp(true)}>
        <BoltIcon />
        Top Up
      </div>
    )
  }

  const renderNotBitcoiner = () => (
    <Box className={classNames('not-bitcoiner-warning', currentUser?.type)}>
      OOOPS! We believe that you might be a {currentUser?.type === 'shitcoiner' ? 'Shitcoiner 💩' : 'Nocoiner 😭' } <span role="button">Learn how you can to fix this</span>
    </Box>
  )

  const viewingOwnProfile = !asDashboard && currentUser && currentUser?.twitter_id === userData?.twitter_id;

  return (
    <div className="__main-profile">
      <header className={classNames(`${userData?.type}`, {'show-background': !handleVisible })}>
        { !publicUser && <HamburgerMenu /> }
        { userData && <div className={classNames("username", { visible: !handleVisible })}>@{ userData?.twitter_username }</div> }
        { asDashboard
        ? renderSatsCounter()
        : <UserTypeBadge userType={userData?.type} variant='outline-white' onClick={viewingOwnProfile ? null : onClickBadge} />
        }
        { !viewingOwnProfile && !asDashboard && handleVisible && (
          <div className="rate-user-tooltip" role="button" onClick={onClickBadge}>
            <div>Vote</div>
            <div className="close">×</div>
          </div>
        )}
      </header>
      <main>
        <div className={classNames("usertype-bg", `${userData?.type}`)} />
        <div className="content">
          <section className="user-details">
            <div ref={profilePicRef}><ProfilePicture user={userData} className='profile-pic' /></div>
            <div className="username">{ userData?.name }</div >
            <div className="handle">@{ userData?.twitter_username }</div>
            { !asDashboard && (
            <>
              <div className="badges">
                <ConnectionTypeBadge type='follows-you' connection={userData} />
                <ConnectionTypeBadge type='following' connection={userData} />
              </div>
              
              <div className="description">{ userData?.twitter_description }</div>
            </>
            )}

            { asDashboard && (
              <>
                <div className='badge'><UserTypeBadge userType={userData?.type} /></div>
                { userData && userData.type !== 'bitcoiner' && renderNotBitcoiner() }
              </>
            )}
            { !asDashboard && !viewingOwnProfile && <ConnectButton connection={userData} onToggle={onToggleConnect} onClickCapture={checkSignedUp} /> }
          </section>

          { userData && (
            <div className="panels">
            { asDashboard && campaignRunning && renderCampaignStats() }
            { asDashboard && renderNetworkStats() }

            <ConnectionsBox connectionType='following' user={userData} isAuthUser={asDashboard} onClickCapture={checkSignedUp} preventActions={publicUser} />
            <ConnectionsBox connectionType='followers' user={userData} isAuthUser={asDashboard} onClickCapture={checkSignedUp} preventActions={publicUser} />
          </div>
          )}
        </div>
      </main>

      { publicUser && (
      <div className="signup-footer">
        <TwitterButton onClick={onClickSignup}>Login Via Twitter</TwitterButton>
      </div>
      )}

      { viewingOwnProfile && (
        <div className="viewing-own-profile">
          You are viewing your own Public Profile. <br /><Link to='/'>Back to dashboard</Link>
        </div>
      )}

      <MassConnectModal show={showMassConnect} onHide={() => setShowMassConnect(false)} />
      <CommunityRateModal show={showRate} onHide={() => setShowRate(false)} user={userData} />
      <TopUpModal show={showTopUp} onHide={() => setShowTopUp(false)} />
      <SignUpModal show={showSignup} onHide={() => setShowSignup(false)} />
    </div>
  );
}