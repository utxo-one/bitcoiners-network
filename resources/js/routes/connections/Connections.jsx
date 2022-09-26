import axios from "axios";
import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import PointyArrow from "../../assets/icons/PointyArrow";
import UserInfoOverlay from "../../components/UserInfoOverlay/UserInfoOverlay";

import './Connections.scss';

export default function Connections({ type }) {

  const [connections, setConnections] = useState(null);
  const [count, setCount] = useState(null);
  const [showInfo, setShowInfo] = useState(false);
  const [selectedConnection, setSelectedConnection] = useState(null);

  const navigate = useNavigate();

  useEffect(() => {
    const loadConnections = async () => {
      
      const { data } = await axios.get(`/frontend/follow/${type}/bitcoiner`);
      setConnections(data[type === 'available' ? 'availableFollows' : type].data);
      setCount(data[type === 'available' ? 'availableFollows' : type].total);
    }

    loadConnections();
  }, []);

  const goBack = () => {
    navigate(-1);
  }

  const onClickConnection = connection => {
    setShowInfo(true);
    setSelectedConnection(connection);
  }

  const renderTitle = () => {
    switch (type) {
      case 'available':
        return 'bitcoiners.network';

      case 'following':
        return <>Following <span className="user-count">({count})</span></>
      
      case 'followers':
        return <>Followers <span className="user-count">({count})</span></>

      default:
        return null;
    }
  }

  return (
    <div className="__connections">
      <header>
        <PointyArrow role='button' className="back" onClick={goBack} />
        <h2>{ renderTitle() }</h2>
        <div className="filter">Filter</div>
      </header>

      <section className="users">
        { connections?.map(connection => (
          <div className="user-details" key={connection.twitter_id} onClick={() => onClickConnection(connection)}>
            <img className="profile" src={connection.twitter_profile_image_url} />
            <div>
              <div className="name">{ connection.name }</div>
              <div className="twitter-handle">@{ connection.twitter_username }</div>

              { connection.follows_authenticated_user && <pre>[ Follows you ]</pre> }
              { connection.is_followed_by_authenticated_user && <pre>[ Followed ]</pre> }
            </div>
          </div>
        ))}
      </section>

      <UserInfoOverlay show={showInfo} user={selectedConnection} onHide={() => setShowInfo(false)} />
    </div>
  );
}
