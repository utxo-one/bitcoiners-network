import axios from "axios";
import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import PointyArrow from "../../assets/icons/PointyArrow";
import UserInfoPanel from "../../components/UserInfoPanel/UserInfoPanel";
import UserTypeBadge from "../../components/UserTypeBadge/UserTypeBadge";

import ConnectionsFilter from "./ConnectionsFilter";
import ConnectionTypeDropdown from "./ConnectionTypeDropdown";
import ProfilePicture from "../../components/ProfilePicture/ProfilePicture";
import CommunityRateModal from "./CommunityRateModal";

import './Connections.scss';
import ConnectionSkeleton from "./ConnectionSkeleton";

export default function Connections({ initialType }) {

  const [type, setType] = useState(initialType);
  const [headerType, setHeaderType] = useState(type);

  const [connections, setConnections] = useState(null);
  const [count, setCount] = useState(null);
  const [showInfo, setShowInfo] = useState(false);
  const [showRate, setShowRate] = useState(false);
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

  const onClickConnection = (e, connection) => {
    setShowInfo(true);
    setSelectedConnection(connection);
  }

  const onSelectConnectionType = async type => {
    const filterPath = filterUserType === 'all' ? '' : filterUserType;

    // the header type should change first for better UX
    setHeaderType(type);

    const { data } = await axios.get(`/frontend/follow/${type}/${filterPath}`);

    setConnections(data[type === 'available' ? 'availableFollows' : type].data);
    setCount(data[type === 'available' ? 'availableFollows' : type].total);

    // type is only changed afterwards to correctly display the follows you / followed by badge:
    setType(type);
  }

  const clickUserTypeBadge = (e, connection) => {
    setShowRate(true);

    // TODO -> do not use stop propagation, instead check with current target and ref:
    e.stopPropagation();
  }

  const renderUsers = () => {
    return (
      <section className="users">
        {/* <ConnectionSkeleton /> */}
        { connections?.map(connection => (
          <div className="user" key={connection.twitter_id} onClick={e => onClickConnection(e, connection)}>
            <ProfilePicture user={connection} />
            <div className="user-details">
              <div className="name-label">
                <div className="overflow-container">
                  <div className="name">{ connection.name }</div>
                  <div className="twitter-handle">@{ connection.twitter_username }</div>
                </div>
                <UserTypeBadge userType={connection.type} onClick={e => clickUserTypeBadge(e, connection)} />
              </div>
              <div className="description">
                { connection.twitter_description }
              </div>

              { type !== 'followers' && connection.follows_authenticated_user && <div className="is-following-badge">Follows you</div> }
              { type !== 'following' && connection.is_followed_by_authenticated_user && <div className="is-following-badge">Followed</div> }
            </div>
          </div>
        ))}
      </section>
    );
  }

  return (
    <div className="__connections">
      <header>
        <PointyArrow role='button' className="back" onClick={goBack} />
        <ConnectionTypeDropdown connectionType={headerType} onSelect={onSelectConnectionType} count={count} />
        <ConnectionsFilter userType={filterUserType} onSelectUserType={setFilterUserType} disabled={type === 'available'} />
      </header>

      { renderUsers() }

      <UserInfoPanel show={showInfo} user={selectedConnection} onHide={() => setShowInfo(false)} />
      <CommunityRateModal show={showRate} onHide={() => setShowRate(false)} />
    </div>
  );
}
