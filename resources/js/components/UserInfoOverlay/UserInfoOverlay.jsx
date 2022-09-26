import { useState } from "react";
import ConnectionsChart from "../../layout/Connections/ConnectionsChart";
import Modal from "../../layout/Modal/Modal";
import { CompactNumberFormat } from "../../utils/NumberFormatting";

import './UserInfoOverlay.scss';

export default function UserInfoOverlay({ show, onHide, user }) {

  const [connectionType, setConnectionType] = useState('followers');

  return (
    <Modal show={show} onHide={onHide} className="__user-info-modal">
      <img className="profile-pic" src={user?.twitter_profile_image_url} />

      <div className="username">{ user?.name }</div >
      <div className="handle">@{ user?.twitter_username }</div>
      <div className="description">{ user?.twitter_description }</div>

      <ConnectionsChart connectionType={connectionType} user={user} showCount={false} />

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
    </Modal>
  )
}
