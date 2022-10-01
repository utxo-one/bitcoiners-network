import { useEffect, useState } from "react";
import axios from "axios";
import { Link } from "react-router-dom";

import Box from "../../layout/Box/Box";
import ConnectionsBox from "./ConnectionsBox";

import SocialNetworkIcon from "../../assets/icons/SocialNetworkIcon";

import './Dashboard.scss';
import MassConnectModal from "../../components/MassConnectModal/MassConnectModal";
import ButtonWithLightning from "../../layout/Button/ButtonWithLightning";
import ProfilePicture from "../../components/ProfilePicture/ProfilePicture";

export default function Dashboard() {

  const [userData, setUserData] = useState(null);
  const [followBitcoiners, setFollowBitcoiners] = useState(null);
  const [showMassConnect, setshowMassConnect] = useState(false);

  useEffect(() => {
    const loadUserData = async () => {
      const { data } = await axios.get('/frontend/user/auth');
      const { data: followData } = await axios.get('/frontend/follow/available/bitcoiner');

      setUserData(data);
      setFollowBitcoiners(followData.availableFollows);
    }

    loadUserData();
  }, []);

  if (!userData) {
    return (
      <pre>[ LOADING... ]</pre>
    );
  }

  return (
    <div className="__dashboard">
      <header />
      <main>
        <section className="user-details">
          <ProfilePicture user={userData} className='profile-pic' />
          <div className="username">{ userData?.name }</div >
          <div className="handle">@{ userData?.twitter_username }</div>
        </section>

        <div className="panels">
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

          <ConnectionsBox connectionType='following' user={userData} />
          <ConnectionsBox connectionType='followers' user={userData} />
        </div>
      </main>

      <MassConnectModal show={showMassConnect} onHide={() => setshowMassConnect(false)} />
    </div>
  );
}