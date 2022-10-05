import { useEffect, useRef, useState } from "react";
import axios from "axios";
import { Link, useParams } from "react-router-dom";
import classNames from "classnames";

import Box from "../../layout/Box/Box";
import ConnectionsBox from "./ConnectionsBox";
import ButtonWithLightning from "../../layout/Button/ButtonWithLightning";

import MassConnectModal from "../../components/MassConnectModal/MassConnectModal";
import ProfilePicture from "../../components/ProfilePicture/ProfilePicture";
import UserTypeBadge from "../../components/UserTypeBadge/UserTypeBadge";
import ConnectionTypeBadge from "../../components/ConnectionTypeBadge/ConnectionTypeBadge";
import CommunityRateModal from "../connections/CommunityRateModal";

import SocialNetworkIcon from "../../assets/icons/SocialNetworkIcon";

import BackNavigation from "../../layout/BackNavigation/BackNavigation";

import './MainProfile.scss';

export default function MainProfile({ asDashboard }) {

  const { username } = useParams();

  const [userData, setUserData] = useState(null);
  const [followBitcoiners, setFollowBitcoiners] = useState(null);
  const [showMassConnect, setshowMassConnect] = useState(false);
  const [handleVisible, setHandleVisible] = useState(false);
  const [showRate, setShowRate] = useState(false);
  
  const profilePicRef = useRef();
  const handleIntersector = useRef();
  
  useEffect(() => {
    const loadUserData = async () => {
      const { data } = await axios.get(`/frontend/user/${asDashboard ? 'auth' : username}`);
      const { data: followData } = await axios.get('/frontend/follow/available/bitcoiner');

      setUserData(data);
      setFollowBitcoiners(followData.availableFollows);
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
    setShowRate(true);
  }

  // if (!userData) {
  //   return (
  //     <pre>[ LOADING... ]</pre>
  //   );
  // }

  return (
    <div className="__main-profile">
      <header className={classNames(`${userData?.type}`, {'show-background': !handleVisible })}>
        { !asDashboard && <BackNavigation /> }
        { userData && <div className={classNames("username", { visible: !handleVisible })}>@{ userData?.twitter_username }</div> }
        <UserTypeBadge userType={userData?.type} variant='outline-white' onClick={onClickBadge} />
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
          </section>

          { userData && (
            <div className="panels">
            { asDashboard && (
              <Box className="bitcoiners-you-follow">
                <div className="data">
                  <div className="label">Bitcoiners you follow</div>
                  <div className="value">{ Number(userData?.following_data.bitcoiners).toLocaleString() }</div>
                </div>
                
                <div className="data">
                  <div className="label">
                    <span className="bitcoiners">bitcoiners</span><span className="network">.network</span> Pool
                  </div>
                  <div className="value pool">{ Number(followBitcoiners?.total).toLocaleString() }</div>
                </div>

                <hr />

                <div className="network-info">
                  <SocialNetworkIcon />
                  <div className="info">You're currently following 1.2% of our <Link to='/available' className="bitcoin-twitter">Bitcoin Twitter</Link> user base.</div>
                </div>

                <hr />

                <ButtonWithLightning className="mass-follow" onClick={() => setshowMassConnect(true)}>
                  <div>Mass Follow</div>
                </ButtonWithLightning>
              </Box>
            )}

            <ConnectionsBox connectionType='following' user={userData} isAuthUser={asDashboard} />
            <ConnectionsBox connectionType='followers' user={userData} isAuthUser={asDashboard} />
          </div>
          )}
        </div>
      </main>

      <MassConnectModal show={showMassConnect} onHide={() => setshowMassConnect(false)} />
      <CommunityRateModal show={showRate} onHide={() => setShowRate(false)} user={userData}  />
    </div>
  );
}