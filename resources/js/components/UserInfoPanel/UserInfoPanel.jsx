import * as Dialog from "@radix-ui/react-dialog";
import classNames from "classnames";
import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import ConnectionsChart from "../../layout/Connections/ConnectionsChart";
import { CompactNumberFormat } from "../../utils/NumberFormatting";
import ProfilePicture from "../ProfilePicture/ProfilePicture";
import UserTypeBadge from "../UserTypeBadge/UserTypeBadge";

import './UserInfoPanel.scss';

const CONNECTION_TYPES = {
  followers: 'Followers',
  following: 'Following',
}

export default function UserInfoPanel({ show, onHide, user, onClickBadge, onClickConnection }) {

  const [connectionType, setConnectionType] = useState('followers');

  // For better UX, reset connections back to 'followers' when overlay is reopened:
  useEffect(() => {
    show && setConnectionType('followers');
  }, [show]);

  return (
    <Dialog.Root open={show} onOpenChange={onHide}>
      <Dialog.Portal>
        <Dialog.Overlay className='__user-info-panel __dialog-overlay'>
          <Dialog.Content className='__user-info-panel-content'>
          <UserTypeBadge userType={user?.type} variant='solid' size='md' onClick={onClickBadge} />
            <ProfilePicture user={user} className="profile-pic" />

            <div className="username">{ user?.name }</div >
            <Link to={`/profile/${user?.twitter_username}`} className="handle">@{ user?.twitter_username }</Link>
            <div className="description">{ user?.twitter_description }</div>

            <div className='connections-tab'>
              { Object.entries(CONNECTION_TYPES).map(([type, phrase]) => (
                <div key={type} role="button" className={classNames('tab', { selected: type === connectionType })} onClick={() => setConnectionType(type)}>
                  { phrase }
                </div>
              ))}
            </div>

            <ConnectionsChart connectionType={connectionType} user={user} showCount={false} onClickDiagram={userType => onClickConnection(userType, connectionType)} />

            <div className="connection-totals">
              <div>
                <div className="count">{ CompactNumberFormat(user?.following_data.total) }</div>
                <div className="label">Following</div>
              </div>
              <div>
                <div className="count">{ CompactNumberFormat(user?.follower_data.total) }</div>
                <div className="label">Followers</div>
              </div>
            </div>
          </Dialog.Content>
        </Dialog.Overlay>
      </Dialog.Portal>
    </Dialog.Root>
  );
}
