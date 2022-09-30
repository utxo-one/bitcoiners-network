import axios from "axios";
import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import PointyArrow from "../../assets/icons/PointyArrow";
import UserInfoPanel from "../../components/UserInfoPanel/UserInfoPanel";
import UserTypeBadge from "../../components/UserTypeLabel/UserTypeBadge";

import ConnectionsFilter from "./ConnectionsFilter";
import ConnectionTypeDropdown from "./ConnectionTypeDropdown";
import ProfilePicture from "../../components/ProfilePicture/ProfilePicture";

import './Connections.scss';

export default function Connections({ initialType }) {

  const [type, setType] = useState(initialType);
  const [connections, setConnections] = useState(null);
  const [count, setCount] = useState(null);
  const [showInfo, setShowInfo] = useState(false);
  const [selectedConnection, setSelectedConnection] = useState(null);
  const [filterUserType, setFilterUserType] = useState(() => type === 'available' ? 'bitcoiner' : 'all');

  const navigate = useNavigate();

  useEffect(() => {
    const loadConnections = async () => {
      const filterPath = filterUserType === 'all' ? '' : filterUserType;
      
      const { data } = await axios.get(`/frontend/follow/${type}/${filterPath}`);

      setConnections(data[type === 'available' ? 'availableFollows' : type].data);
      setCount(data[type === 'available' ? 'availableFollows' : type].total);
    }

    loadConnections();
  }, [filterUserType, type]);

  const goBack = () => {
    navigate(-1);
  }

  const onClickConnection = connection => {
    setShowInfo(true);
    setSelectedConnection(connection);
  }
  
  return (
    <div className="__connections">
      <header>
        <PointyArrow role='button' className="back" onClick={goBack} />
        <ConnectionTypeDropdown connectionType={type} onSelect={setType} count={count} />
        <ConnectionsFilter userType={filterUserType} onSelectUserType={setFilterUserType} disabled={type === 'available'} />
      </header>

      <section className="users">
        { connections?.map(connection => (
          <div className="user" key={connection.twitter_id} onClick={() => onClickConnection(connection)}>
            <ProfilePicture user={connection} />
            <div className="user-details">
              <div className="name-label">
                <div className="overflow-container">
                  <div className="name">{ connection.name }</div>
                  <div className="twitter-handle">@{ connection.twitter_username }</div>
                </div>
                <UserTypeBadge userType={connection.type} />
              </div>
              <div className="description">
                { connection.twitter_description }
              </div>

              { connection.follows_authenticated_user && <pre>[ Follows you ]</pre> }
              { connection.is_followed_by_authenticated_user && <pre>[ Followed ]</pre> }
            </div>
          </div>
        ))}
      </section>

      <UserInfoPanel show={showInfo} user={selectedConnection} onHide={() => setShowInfo(false)} />
    </div>
  );
}
