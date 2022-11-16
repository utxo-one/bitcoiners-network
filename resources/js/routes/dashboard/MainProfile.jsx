import { useContext, useEffect, useRef, useState } from "react";
import { useImmer } from "use-immer";
import axios from "axios";
import { Link, useParams, useSearchParams } from "react-router-dom";
import classNames from "classnames";
import { CompactNumberFormat } from "../../utils/NumberFormatting";
import Cookies from 'js-cookie';
import useEndorsements from "../../hooks/useEndorsements";

import AppContext from "../../store/AppContext";

import Box from "../../layout/Box/Box";
import ConnectionsBox from "./ConnectionsBox";
import ButtonWithLightning from "../../layout/Button/ButtonWithLightning";
import TwitterButton from "../../layout/Button/TwitterButton";
import Button from "../../layout/Button/Button";
import ConnectButton from "../../layout/Button/ConnectButton";
import HamburgerMenu from "../../layout/HamburgerMenu/HamburgerMenu";

import ProfilePicture from "../../components/ProfilePicture/ProfilePicture";
import UserTypeBadge from "../../components/UserTypeBadge/UserTypeBadge";
import ConnectionTypeBadge from "../../components/ConnectionTypeBadge/ConnectionTypeBadge";
import CampaignStats from "../../components/CampaignStats/CampaignStats";

import MassConnectModal from "../../components/MassConnectModal/MassConnectModal";
import CommunityRateModal from "../connections/CommunityRateModal";
import TopUpModal from "../../components/MassConnectModal/TopUpModal";
import SignUpModal from "./SignUpModal";
import NotBitcoinerModal from "./NotBitcoinerModal";
import EndorsementBadges from "../../components/EndorsementBadges/EndorsementBadges";
import EndorsementModal from "../connections/EndorsementModal";

import SocialNetworkIcon from "../../assets/icons/SocialNetworkIcon";
import SatsIcon from "../../assets/icons/SatsIcon";
import BoltIcon from "../../assets/icons/BoltIcon";

import './MainProfile.scss';
import VoteTooltip from "../../layout/VoteTooltip/VoteTooltip";
import Spinner from "../../layout/Spinner/Spinner";

export default function MainProfile({ asDashboard }) {
  const [state, dispatch] = useContext(AppContext);

  const { username } = useParams();
  const [searchParams] = useSearchParams();

  const { loadEndorsements } = useEndorsements();

  const [loadedUser, setLoadedUser] = useImmer(null);
  const [campaignData, setCampaignData] = useState(null);
  const [showMassConnect, setShowMassConnect] = useState(false);
  const [handleVisible, setHandleVisible] = useState(false);
  const [showRate, setShowRate] = useState(false);
  const [showTopUp, setShowTopUp] = useState(false);
  const [showSignup, setShowSignup] = useState(false);
  const [initialLoad, setInitialLoad] = useState(false);
  const [showNotBitcoiner, setShowNotBitcoiner] = useState(false);
  const [firstTimeLogin, setFirstTimeLogin] = useState(false);
  const [userNotFound, setUserNotFound] = useState(false);
  const [showEndorsements, setShowEndorsements] = useState(false);
  
  const profilePicRef = useRef();
  const handleIntersector = useRef();
  
  const { currentUser, metrics, publicUser, availableSats, requestsLoaded } = state;

  const userData = asDashboard ? currentUser : loadedUser;

  useEffect(() => {
    if (asDashboard && userData) {
      // Keep in session storage (IE: while browser is open) to prevent the user from pressing back and seeing the modal:
      const firstLogin = searchParams.get('firstLogin') === '1';
      const cookie = Cookies.get("__bn__first_login_shown");
  
      if (firstLogin && !cookie) {
        Cookies.set("__bn__first_login_shown", true);
        
        if (['shitcoiner', 'nocoiner'].includes(userData.type)) {
          setFirstTimeLogin(true);
          setShowNotBitcoiner(true);
        }
      }
    }
  }, [searchParams, userData]);

  useEffect(() => {
    const loadUserData = async () => {
      if (!asDashboard) {
        try {
          const { data } = await axios.get(`/frontend/user/${username}`);
          setLoadedUser(data);

          const { endorsements, endorsements_auth } = await loadEndorsements(username);

          endorsements && setLoadedUser(draft => {
            draft._endorsements = endorsements;
            draft._endorsements_auth = endorsements_auth;
          });
        }
        catch {
          setUserNotFound(true);
        }
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

    setLoadedUser(null);
    setUserNotFound(false);
    setInitialLoad(false);

    loadUserData();
  }, [asDashboard, username]);

  useEffect(() => {
    const loadFollowData = async () => {
      if (userData && !userData.following_data) {
        const { data } = await axios.get(`/frontend/user/${userData.twitter_username}/follow-data`);
        
        if (asDashboard) {
          dispatch({ type: 'currentUser/set-follow-data', payload: data });
        }
        else {
          setLoadedUser(draft => {
            draft.following_data = data.following_data;
            draft.follower_data = data.follower_data;
          });
        }
      }
    }
    
    if (requestsLoaded && initialLoad) {
      loadFollowData();
    }

  }, [requestsLoaded, initialLoad])

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
    setShowSignup(true);
  }

  // TODO -> change conditional
  const checkSignedUp = e => {
    if (publicUser) {
      e.stopPropagation();
      setShowSignup(true);
    }
  }

  const onHideNotBitcoiner = () => {
    setShowNotBitcoiner(false);
    setFirstTimeLogin(false);
  }

  const updateEndorsement = type => {
    setLoadedUser(draft => {
      const prevEndorsed = draft._endorsements_auth[type] !== 0;

      draft._endorsements_auth[type] = prevEndorsed ? 0 : 1;
      draft._endorsements[type] += prevEndorsed ? -1 : 1;
    })
  }

  const onClickAddEndorsements = () => {
    if (publicUser) {
      setShowSignup(true);
    }
    else {
      setShowEndorsements(true);
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
      OOOPS! We believe that you might be a {currentUser?.type === 'shitcoiner' ? 'Shitcoiner 💩' : 'Nocoiner 🤡' } <span role="button" onClick={() => setShowNotBitcoiner(true)}>Learn how you can to fix this</span>
    </Box>
  )

  const renderUserSkeleton = () => (
    <div className="user-skeleton">
      <div className="bone username" />
      <div className="bone handle" />
      <div className="bone description" />
    </div>
  )

  const viewingOwnProfile = !asDashboard && currentUser && currentUser?.twitter_id === userData?.twitter_id;

  return (
    <div className="__main-profile">
      <header className={classNames(`${userData?.type}`, {'show-background': !handleVisible })}>
        { !publicUser && <HamburgerMenu /> }
        { userData && <div className={classNames("username", { visible: !handleVisible })}>@{ userData?.twitter_username }</div> }
        { asDashboard
        ? renderSatsCounter()
        : <UserTypeBadge userType={userData?.type} variant='outline-white' role={ viewingOwnProfile ? null : "button"} onClick={viewingOwnProfile ? null : onClickBadge} />
        }
        { !publicUser && !viewingOwnProfile && !asDashboard && handleVisible && userData && (
          <VoteTooltip arrowDirection="up" />
        )}
      </header>
      <main>
        <div className={classNames("usertype-bg", `${userData?.type}`)} />
        <div className="content">
          <section className="user-details">
            <div ref={profilePicRef}><ProfilePicture user={userData} className='profile-pic' userNotFound={userNotFound} /></div>
            
            { !userData && !userNotFound && renderUserSkeleton() }

            <div className="username">{ userData?.name }</div >
            { userData && <div className="handle">@{ userData?.twitter_username }</div> }
            { userNotFound && (
              <Box className="user-not-found">
                <h2>User Not Found</h2>
                <hr />
                <p>Ooops! We don't have a public profile for user @<strong>{username}</strong> yet (or the user doesn't exist).</p>

                { !asDashboard && (
                  <>
                    <p>We are relentessly scanning for users. If this is your handle, <a href='/get-started'>sign in via Twitter</a> to get your user profile into our network.</p>
                    <p className="back-to-dashboard"><Link to='dashboard'>Back to dashboard</Link></p>
                  </>
                )}
              </Box>
            )}
            { !asDashboard && (
            <>
              <div className="badges">
                <ConnectionTypeBadge type='follows-you' connection={userData} />
                <ConnectionTypeBadge type='following' connection={userData} />
              </div>
              
              <div className='endorsements'><EndorsementBadges user={userData} onClick={onClickAddEndorsements} viewingOwnProfile={viewingOwnProfile} /></div>
              <div className="description">{ userData?.twitter_description }</div>
            </>
            )}

            { asDashboard && (
              <>
                <div className='type-badge'><UserTypeBadge userType={userData?.type} /></div>
                { userData && userData.type !== 'bitcoiner' && renderNotBitcoiner() }
              </>
            )}
            { !asDashboard && !viewingOwnProfile && <ConnectButton connection={userData} onToggle={onToggleConnect} onClickCapture={checkSignedUp} /> }
          </section>

          { userData && (
            <div className="panels">
            { asDashboard && campaignRunning && renderCampaignStats() }
            { asDashboard && userData.follower_data && renderNetworkStats() }

            { userData.follower_data 
            ? <>
                <ConnectionsBox connectionType='following' user={userData} isAuthUser={asDashboard} onClickCapture={checkSignedUp} preventActions={publicUser} />
                <ConnectionsBox connectionType='followers' user={userData} isAuthUser={asDashboard} onClickCapture={checkSignedUp} preventActions={publicUser} />
              </>
            : <div className="followers-loading"><Spinner /></div>
            }
          </div>
          )}

          { userData?.follower_data && (
            <ul className="info-links">
              <li><a href="/terms" target="_blank">Terms of Service</a></li>
              <li><a href="/privacy" target="_blank">Privacy Policy</a></li>
            </ul>
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
      <NotBitcoinerModal user={userData} show={showNotBitcoiner} onHide={onHideNotBitcoiner} firstTimeLogin={firstTimeLogin} />
      <EndorsementModal show={showEndorsements} onHide={() => setShowEndorsements(false)} user={userData} onToggleEndorsement={updateEndorsement} />
    </div>
  );
}