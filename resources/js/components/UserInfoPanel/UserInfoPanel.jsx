import * as Dialog from "@radix-ui/react-dialog";
import classNames from "classnames";
import { useContext, useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import EndorseIcon from "../../assets/icons/EndorseIcon";
import StarIcon from "../../assets/icons/StarIcon";
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
  const navigate = useNavigate();

  const { currentUser } = state;

  const viewingOwnProfile = currentUser && currentUser?.twitter_id === user?.twitter_id;

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
                <div>Rate User</div>
                <div className="close">×</div>
              </div>
            )}

            <UserTypeBadge userType={user?.type} variant='solid' size='md' onClick={viewingOwnProfile ? null : onClickBadge} />
            <ProfilePicture user={user} className="profile-pic" />

            <div className="username">{ user?.name }</div >
            <Link to={`/profile/${user?.twitter_username}`} className="handle">@{ user?.twitter_username }</Link>
            <div className="description">{ user?.twitter_description }</div>

            {/* { !viewingOwnProfile && (
              <div className="user-actions">
                <div className="action" role="button" onClick={onClickBadge}>
                  <StarIcon />
                  Rate User
                </div>

                <div className="action" role="button">
                  <EndorseIcon />
                  Endorse
                </div>
              </div>
            )} */}

            <div className='connections-tab'>
              { Object.entries(CONNECTION_TYPES).map(([type, phrase]) => (
                <div key={type} role="button" className={classNames('tab', { selected: type === connectionType })} onClick={() => setConnectionType(type)}>
                  { phrase }
                </div>
              ))}
            </div>

            <ConnectionsChart connectionType={connectionType} user={user} showCount={false} onClickDiagram={userType => redirectOnConnectionClick(userType)} />

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
