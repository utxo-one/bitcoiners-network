import axios from "axios";
import { useState, useEffect, useRef } from "react";
import { useLocation, useNavigate, useParams } from "react-router-dom";
import useEventCallback from "../../hooks/useEventCallback";

import UserInfoPanel from "../../components/UserInfoPanel/UserInfoPanel";
import UserTypeBadge from "../../components/UserTypeBadge/UserTypeBadge";
import ConnectionsFilter from "./ConnectionsFilter";
import ConnectionTypeDropdown from "./ConnectionTypeDropdown";
import ProfilePicture from "../../components/ProfilePicture/ProfilePicture";
import CommunityRateModal from "./CommunityRateModal";

import Spinner from "../../layout/Spinner/Spinner";
import PointyArrow from "../../assets/icons/PointyArrow";

import './Connections.scss';
import ConnectionTypeBadge from "../../components/ConnectionTypeBadge/ConnectionTypeBadge";
import CenteredSpinner from "../../layout/Spinner/CenteredSpinner";

export default function Connections({ initialType }) {

  const location = useLocation();
  const { initialUserType } = location.state || {};

  const { username = null } = useParams();
  const [type, setType] = useState(initialType);
  const [headerType, setHeaderType] = useState(type);

  // const [username, setUsername] = useState(null);
  // const [username, setUsername] = useState('bluewalletio');

  const [initialLoad, setInitialLoad] = useState(false);
  const [loadMore, setLoadMore] = useState(false);
  const [loadedAllItems, setLoadedAllItems] = useState(false);

  const [connectionsData, setConnectionsData] = useState(null);
  const [connections, setConnections] = useState(null);
  const [count, setCount] = useState(null);
  const [showInfo, setShowInfo] = useState(false);
  const [showRate, setShowRate] = useState(false);
  const [selectedConnection, setSelectedConnection] = useState(null);
  const [filterUserType, setFilterUserType] = useState(() => initialUserType || (type === 'available' ? 'bitcoiner' : 'all'));
  const infiniteLoaderRef = useRef();
  const infiniteLoaderObserver = useRef();

  const navigate = useNavigate();

  useEffect(() => {
    const loadConnections = async () => {
      const filterPath = filterUserType === 'all' ? '' : filterUserType;
      
      const getPath = username === null
      ? `/frontend/follow/${headerType}/${filterPath}`
      : `/frontend/follow/user/${headerType}/${username}/${filterPath}`
      
      const { data } = await axios.get(getPath);

      const responseConnections = data[headerType === 'available' ? 'availableFollows' : headerType];

      // entries will change, so scroll to top to make sure the correct ones are displayed
      window.scrollTo(0, 0);

      setConnectionsData(responseConnections);

      setLoadedAllItems(responseConnections.current_page === responseConnections.last_page);
      setConnections(responseConnections.data);
      setCount(responseConnections.total);

      setInitialLoad(true);
      setType(headerType);
    }

    loadConnections();
  }, [username, filterUserType, headerType]);

  const loadMoreItems = useEventCallback(() => {
    setLoadMore(true);

    console.log('loadMore:', loadMore)

    if (loadMore) {
      return;
    }

    const loadItems = async () => {
      const filterPath = filterUserType === 'all' ? '' : filterUserType;
      const page = connectionsData.current_page + 1;

      const { data } = await axios.get(`/frontend/follow/${type}/${filterPath}?page=${page}`);
      const responseConnections = data[type === 'available' ? 'availableFollows' : type];

      if (responseConnections.current_page === responseConnections.last_page) {
        setLoadedAllItems(true);
      }

      setConnectionsData(responseConnections);

      setConnections([...connections, ...responseConnections.data]);
      setLoadMore(false);

    }

    loadItems();
  });

  useEffect(() => {
    if (initialLoad) {
      const checkIntersection = entries => {
        entries.forEach(entry => entry.isIntersecting && loadMoreItems());
      }

      infiniteLoaderObserver.current?.disconnect();

      if (infiniteLoaderRef.current) {
        infiniteLoaderObserver.current = new IntersectionObserver(checkIntersection);
        infiniteLoaderObserver.current.observe(infiniteLoaderRef.current);
      }

      setInitialLoad(false);
    }
  }, [initialLoad]);

  const goBack = () => {
    navigate(-1);
  }

  const onClickConnection = (e, connection) => {
    setShowInfo(true);
    setSelectedConnection(connection);
  }

  const onSelectConnectionType = async type => {
    // the header type should change first for better UX
    setHeaderType(type);

    if (type === 'available' && filterUserType === 'all') {
      setFilterUserType('bitcoiner');
    }
  }

  const clickUserTypeBadge = (e, user) => {
    setShowRate(true);
    setSelectedConnection(user);

    // TODO -> do not use stop propagation, instead check with current target and ref:
    e.stopPropagation();
  }

  const onClickPanelConnections = (userType, connectionType) => {
    setShowInfo(false);
    // setUsername(selectedConnection.twitter_username);

    navigate(`/${connectionType}/${selectedConnection.twitter_username}`);
    console.log('connectionType:', connectionType)
    setFilterUserType(userType);
    setHeaderType(connectionType);
  }

  const renderUsers = () => {
    if (!connections) {
      return <CenteredSpinner />
    }

    else if (connections.length === 0) {
      return (
        <div className="no-connections-found">
          No connections found.
        </div>
      )
    }

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

              {/* { type !== 'followers' && connection.follows_authenticated_user && <div className="is-following-badge">Follows you</div> }
              { type !== 'following' && connection.is_followed_by_authenticated_user && <div className="is-following-badge">Followed</div> } */}

              { type !== 'followers' && <ConnectionTypeBadge type='follows-you' connection={connection} /> }
              { type !== 'following' && <ConnectionTypeBadge type='following' connection={connection} /> }
            </div>
          </div>
        ))}
        { !loadedAllItems && (
          <div className='infinite-loader' ref={infiniteLoaderRef}>
            <Spinner />
          </div>
        )}
      </section>
    );
  }

  return (
    <div className="__connections">
      <header>
        <PointyArrow role='button' className="back" onClick={goBack} />
        <div className='username-connection-type'> 
          { username && <div className='username'>@{username }</div> }
          <ConnectionTypeDropdown showNetwork={!username} connectionType={headerType} onSelect={onSelectConnectionType} count={count} />
        </div>
        <ConnectionsFilter connectionType={headerType} userType={filterUserType} onSelectUserType={setFilterUserType} />
      </header>

      { renderUsers() }

      <UserInfoPanel show={showInfo} onClickBadge={() => setShowRate(true)} user={selectedConnection} onHide={() => setShowInfo(false)} onClickConnection={onClickPanelConnections} />
      <CommunityRateModal show={showRate} onHide={() => setShowRate(false)} user={selectedConnection} />
    </div>
  );
}
