import { useState } from 'react';
import ProfilePicture from '../../components/ProfilePicture/ProfilePicture';
import UserInfoPanel from '../../components/UserInfoPanel/UserInfoPanel';
import Checkbox from '../../layout/Checkbox/Checkbox';
import InfiniteLoader from '../../layout/Spinner/InfiniteLoader';
import { CompactNumberFormat } from '../../utils/NumberFormatting';
import './CampaignUsers.scss';

export default function CampaignUsers({ campaign, pendingUsers, loadedAllPending, selected, onToggleSelected, onLoadMorePending }) {

  const [showInfo, setShowInfo] = useState(false);
  const [user, setUser] = useState(null);

  const onClickUser = user => {
    setUser(user);
    setShowInfo(true);
  }

  const renderUsersList = () => {

    const list = campaign ? campaign.recentCompletedFollows : pendingUsers;
    
    return list.map(entity => {
      const user = entity.follow;
      
      return (
        <tr key={user.twitter_id} onClick={() => onClickUser(user) } role='button'>
          { !campaign && <td><Checkbox checked={selected.has(user.twitter_id) || false} onChange={e => onToggleSelected(e, user)} onClick={e => e.stopPropagation()} /></td> }
          <td><ProfilePicture user={user} /></td>
          <td className='user-info'>
            <div className='username'>{ user.name }</div>
            <div className='handle'>@{ user.twitter_username }</div>
          </td>
          <td className='followers'>{ CompactNumberFormat(user.twitter_count_followers, { digits: 3 }) }</td>
        </tr>
      );
    });
  }

  return (
    <>
      <table className="__campaign-users">
        <thead>
          <tr>
            { !campaign && <th className='user-checkbox' /> }
            <th className='profile-picture'/>
            <th />
            <th className='followers'>Followers</th>
          </tr>
        </thead>

        <tbody>
          { renderUsersList() }
        </tbody>
      </table>

      { pendingUsers && !loadedAllPending && <InfiniteLoader onLoadMore={onLoadMorePending} /> }

      <UserInfoPanel show={showInfo} user={user} onHide={() => setShowInfo(false)} />
    </>
  );
}
