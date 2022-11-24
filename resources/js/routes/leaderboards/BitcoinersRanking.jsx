import { useState } from 'react';
import ProfilePicture from '../../components/ProfilePicture/ProfilePicture';
import UserInfoPanel from '../../components/UserInfoPanel/UserInfoPanel';
import InfiniteLoader from '../../layout/Spinner/InfiniteLoader';
import { CompactNumberFormat } from '../../utils/NumberFormatting';
import './BitcoinersRanking.scss';

export default function BitcoinersRanking({ users, onClickUser, loadedAllPending, selected, onToggleSelected, onLoadMorePending }) {
  const renderUsersList = () => {

    return users?.filter((_, index) => index < 15).map((entity, index) => {
      const user = entity.user;
      
      return (
        <tr key={user.twitter_id} onClick={() => onClickUser(user) } role='button'>
          <td className='rank'>{ index + 1 }</td>
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
      <table className="__bitcoiners-ranking">
        <thead>
          <tr>
            <th className='rank'>#</th>
            <th className='profile-picture'/>
            <th />
            <th className='followers'>Followers</th>
          </tr>
        </thead>

        <tbody>
          { renderUsersList() }
        </tbody>
      </table>

      {/* { pendingUsers && !loadedAllPending && <InfiniteLoader onLoadMore={onLoadMorePending} /> } */}
    </>
  );
}
