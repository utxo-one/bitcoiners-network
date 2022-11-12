import { useContext, useEffect, useState, useMemo } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useImmer } from "use-immer";
import classNames from "classnames";
import AppContext from "../../store/AppContext";
import runes from "runes";
import axios from "axios";
import * as Dialog from "@radix-ui/react-dialog";
import { CompactNumberFormat } from "../../utils/NumberFormatting";

import Box from "../../layout/Box/Box";
import ConnectButton from "../../layout/Button/ConnectButton";
import ConnectionsChart from "../../layout/Connections/ConnectionsChart";
import ProfilePicture from "../ProfilePicture/ProfilePicture";
import UserTypeBadge from "../UserTypeBadge/UserTypeBadge";
import Spinner from "../../layout/Spinner/Spinner";
import VoteTooltip from "../../layout/VoteTooltip/VoteTooltip";
import EndorsementBadges from "../EndorsementBadges/EndorsementBadges";

import './UserInfoPanel.scss';

const CONNECTION_TYPES = {
  followers: 'Followers',
  following: 'Following',
}

export default function UserInfoPanel({ show, onHide, user, onClickBadge, onClickConnection, onToggleFollow, onClickEndorse }) {

  const [state] = useContext(AppContext);

  const [connectionType, setConnectionType] = useState('followers');
  const [userWithFollowData, setUserWithFollowData] = useImmer(null);
  const navigate = useNavigate();

  const description = user?.twitter_description;

  const trimmedDescription = useMemo(() => {
    const MAX_LENGTH = 80;

    if (!description) {
      return null;
    }

    const descRunes = runes(description);

    if (descRunes.length > MAX_LENGTH + 3) {
      return runes.substr(description, 0, MAX_LENGTH) + '...';
    }

    return description;
  }, [description]);

  const { currentUser } = state;

  const viewingOwnProfile = currentUser && currentUser?.twitter_id === user?.twitter_id;

  // Refetch user data when panel is shown:
  useEffect(() => {
    const loadFollowData = async () => {
      const { data } = await axios.get(`/frontend/user/${user.twitter_username}/follow-data`);

      setUserWithFollowData(draft => {
        draft.following_data = data.following_data;
        draft.follower_data = data.follower_data;
      });
    }
    
    setUserWithFollowData(user);
    
    show && loadFollowData();
  }, [show]);

  // For better UX, reset connections back to 'followers' when overlay is reopened:
  useEffect(() => {
    show && setConnectionType('followers');
  }, [show]);

  const redirectOnConnectionClick = userType => {
    if (onClickConnection) {
      onClickConnection(userType, connectionType);
    }
    else {
      navigate(`/${connectionType}/${user.twitter_username}`, { state: { initialUserType: userType } });
    }
  }

  const renderEndorsements = () => {
    return <EndorsementBadges user={user} onClick={onClickEndorse} viewingOwnProfile={viewingOwnProfile} />
  }

  return (
    <Dialog.Root open={show} onOpenChange={onHide}>
      <Dialog.Portal>
        <Dialog.Overlay className='__user-info-panel __dialog-overlay'>
          <Dialog.Content className='__user-info-panel-content'>

            {!viewingOwnProfile && (
              <VoteTooltip arrowDirection="down" />
            )}

            <UserTypeBadge userType={user?.type} variant='solid' size='md' onClick={viewingOwnProfile ? null : onClickBadge} />
            <ProfilePicture user={user} className="profile-pic" />

            <div className="username">{ user?.name }</div >
            <Link to={`/profile/${user?.twitter_username}`} className="handle">@{ user?.twitter_username }</Link>

            { renderEndorsements() }
            <div className="description">{ trimmedDescription }</div>

            <div className='connections-tab'>
              { Object.entries(CONNECTION_TYPES).map(([type, phrase]) => (
                <div key={type} role="button" className={classNames('tab', { selected: type === connectionType })} onClick={() => setConnectionType(type)}>
                  { phrase }
                </div>
              ))}
            </div>
  
            <div className='follow-data'>
              <div className={classNames('loading', { hidden: userWithFollowData?.following_data })}><Spinner /></div>
              <ConnectionsChart connectionType={connectionType} user={userWithFollowData} showCount={false} onClickDiagram={userType => redirectOnConnectionClick(userType)} />
            </div>

            <div className="connection-totals">
              <div>
                <div className="count">{ CompactNumberFormat(user?.twitter_count_following, { digits: 4 }) }</div>
                <div className="label">Following</div>
              </div>
              <div>
                <div className="count">{ CompactNumberFormat(user?.twitter_count_followers, { digits: 4 }) }</div>
                <div className="label">Followers</div>
              </div>
            </div>
        
            { viewingOwnProfile
            ? <Box className="viewing-own-profile">You are viewing your own Profile</Box>
            : <ConnectButton connection={user} onToggle={onToggleFollow} />
            }
          </Dialog.Content>
        </Dialog.Overlay>
      </Dialog.Portal>
    </Dialog.Root>
  );
}
