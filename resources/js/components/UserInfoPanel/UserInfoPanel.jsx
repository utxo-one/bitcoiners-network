import * as Dialog from "@radix-ui/react-dialog";
import classNames from "classnames";
import { useImmer } from "use-immer";
import { useContext, useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import Box from "../../layout/Box/Box";
import ConnectButton from "../../layout/Button/ConnectButton";
import ConnectionsChart from "../../layout/Connections/ConnectionsChart";
import AppContext from "../../store/AppContext";
import { CompactNumberFormat } from "../../utils/NumberFormatting";
import ProfilePicture from "../ProfilePicture/ProfilePicture";
import UserTypeBadge from "../UserTypeBadge/UserTypeBadge";

import './UserInfoPanel.scss';

const CONNECTION_TYPES = {
  followers: 'Followers',
  following: 'Following',
}

export default function UserInfoPanel({ show, onHide, user, onClickBadge, onClickConnection, onToggleFollow }) {

  const [state] = useContext(AppContext);

  const [connectionType, setConnectionType] = useState('followers');
  const [userWithFollowData, setUserWithFollowData] = useImmer(null);
  const navigate = useNavigate();

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

  return (
    <Dialog.Root open={show} onOpenChange={onHide}>
      <Dialog.Portal>
        <Dialog.Overlay className='__user-info-panel __dialog-overlay'>
          <Dialog.Content className='__user-info-panel-content'>

            {!viewingOwnProfile && (
              <div className="rate-user-tooltip" role="button" onClick={onClickBadge}>
                <div>Vote</div>
                <div className="close">×</div>
              </div>
            )}

            <UserTypeBadge userType={user?.type} variant='solid' size='md' onClick={viewingOwnProfile ? null : onClickBadge} />
            <ProfilePicture user={user} className="profile-pic" />

            <div className="username">{ user?.name }</div >
            <Link to={`/profile/${user?.twitter_username}`} className="handle">@{ user?.twitter_username }</Link>
            <div className="description">{ user?.twitter_description }</div>

            { userWithFollowData?.following_data && (
              <>
                <div className='connections-tab'>
                  { Object.entries(CONNECTION_TYPES).map(([type, phrase]) => (
                    <div key={type} role="button" className={classNames('tab', { selected: type === connectionType })} onClick={() => setConnectionType(type)}>
                      { phrase }
                    </div>
                  ))}
                </div>
    
                <ConnectionsChart connectionType={connectionType} user={userWithFollowData} showCount={false} onClickDiagram={userType => redirectOnConnectionClick(userType)} />
              </>
            )}

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
