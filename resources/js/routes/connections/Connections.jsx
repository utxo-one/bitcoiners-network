import axios from "axios";
import { useState, useEffect, useMemo } from "react";
import { useImmer } from "use-immer";
import { useLocation, useNavigate, useParams } from "react-router-dom";
import useEventCallback from "../../hooks/useEventCallback";
import useEndorsements from "../../hooks/useEndorsements";

import UserInfoPanel from "../../components/UserInfoPanel/UserInfoPanel";
import UserTypeBadge from "../../components/UserTypeBadge/UserTypeBadge";
import ConnectionsFilter from "./ConnectionsFilter";
import ConnectionTypeDropdown from "./ConnectionTypeDropdown";
import ProfilePicture from "../../components/ProfilePicture/ProfilePicture";
import CommunityRateModal from "./CommunityRateModal";
import EndorsementModal from "./EndorsementModal";
import HamburgerMenu from "../../layout/HamburgerMenu/HamburgerMenu";

import ConnectionTypeBadge from "../../components/ConnectionTypeBadge/ConnectionTypeBadge";
import CenteredSpinner from "../../layout/Spinner/CenteredSpinner";
import InfiniteLoader from "../../layout/Spinner/InfiniteLoader";

import './Connections.scss';

export default function Connections({ initialType }) {

  const location = useLocation();
  const { initialUserType } = location.state || {};

  const { username = null } = useParams();
  const { loadEndorsements } = useEndorsements();

  const [type, setType] = useState(initialType);
  const [headerType, setHeaderType] = useState(type);

  const [initialLoad, setInitialLoad] = useState(false);
  const [loadMore, setLoadMore] = useState(false);
  const [loadedAllItems, setLoadedAllItems] = useState(false);

  const [connectionsData, setConnectionsData] = useState(null);
  const [connections, setConnections] = useImmer(null);
  const [count, setCount] = useState(null);
  const [showInfo, setShowInfo] = useState(false);
  const [showRate, setShowRate] = useState(false);
  const [showEndorsements, setShowEndorsements] = useState(false);
  const [selectedConnectionId, setSelectedConnectionId] = useState(null);
  const [filterUserType, setFilterUserType] = useState(() => initialUserType || (type === 'available' ? 'bitcoiner' : 'all'));

  const selectedConnection = useMemo(() => {
    return connections?.find(user => user.twitter_id === selectedConnectionId);
  }, [selectedConnectionId, connections]);

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
    if (loadMore) {
      return;
    }

    setLoadMore(true);

    const loadItems = async () => {
      const filterPath = filterUserType === 'all' ? '' : filterUserType;
      const page = connectionsData.current_page + 1;

      const { data } = await axios.get(`/frontend/follow/${type}/${filterPath}?page=${page}`);
      const responseConnections = data[type === 'available' ? 'availableFollows' : type];

      if (responseConnections.current_page === responseConnections.last_page) {
        setLoadedAllItems(true);
      }

      setConnectionsData(responseConnections);

      setConnections(draft => {
        draft.push(...responseConnections.data);
      });

      setLoadMore(false);

    }

    loadItems();
  });

  const updateEndorsement = type => {
    setConnections(draft => {
      const index = draft.findIndex(user => user.twitter_id === selectedConnection.twitter_id);

      const prevEndorsed = draft[index]._endorsements_auth[type] !== 0;
      
      draft[index]._endorsements_auth[type] = prevEndorsed ? 0 : 1;
      draft[index]._endorsements[type] += prevEndorsed ? -1 : 1;
    });
  }

  const onClickConnection = async (e, connection) => {
    setShowInfo(true);
    setSelectedConnectionId(connection.twitter_id);

    if (!connection._endorsements) {
      const { endorsements, endorsements_auth } = await loadEndorsements(connection.twitter_username);

      endorsements && setConnections(draft => {
        const index = draft.findIndex(user => user.twitter_id === connection.twitter_id);
        draft[index]._endorsements = endorsements;
        draft[index]._endorsements_auth = endorsements_auth
      });
    }
  }

  const onToggleFollow = () => {
    setConnections(draft => {
      const index = draft.findIndex(user => user.twitter_id === selectedConnection.twitter_id);
      draft[index].is_followed_by_authenticated_user = !draft[index].is_followed_by_authenticated_user;

      // remove from the list of 'following' by setting is as deleted, so that if the user is toggled back to followed, it appears back:
      if (type === 'following') {
        draft[index]._deleted_follow = !draft[index].is_followed_by_authenticated_user;
      }
    });
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
    setSelectedConnectionId(user.twitter_id);

    // TODO -> do not use stop propagation, instead check with current target and ref:
    e.stopPropagation();
  }

  const onClickPanelConnections = (userType, connectionType) => {
    setShowInfo(false);
    navigate(`/${connectionType}/${selectedConnection.twitter_username}`);
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
        { connections?.filter(connection => !connection._deleted_follow).map(connection => (
          <div className="user" role="button" key={connection.twitter_id} onClick={e => onClickConnection(e, connection)}>
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
        { !loadedAllItems && <InfiniteLoader onLoadMore={loadMoreItems} /> }
      </section>
    );
  }

  return (
    <div className="__connections">
      <header>
        <HamburgerMenu variant="inverted" />
        {/* <PointyArrow role='button' className="back" onClick={goBack} /> */}
        <div className='username-connection-type'> 
          { username && <div className='username'>@{username }</div> }
          <ConnectionTypeDropdown showNetwork={!username} connectionType={headerType} onSelect={onSelectConnectionType} count={count} />
        </div>
        <ConnectionsFilter connectionType={headerType} userType={filterUserType} onSelectUserType={setFilterUserType} />
      </header>

      { renderUsers() }

      <UserInfoPanel show={showInfo} onClickBadge={() => setShowRate(true)} user={selectedConnection} onHide={() => setShowInfo(false)} onClickConnection={onClickPanelConnections} onToggleFollow={onToggleFollow} onClickEndorse={() => setShowEndorsements(true)} />
      <CommunityRateModal show={showRate} onHide={() => setShowRate(false)} user={selectedConnection} />
      <EndorsementModal show={showEndorsements} onHide={() => setShowEndorsements(false)} user={selectedConnection} onToggleEndorsement={updateEndorsement} />
    </div>
  );
}
