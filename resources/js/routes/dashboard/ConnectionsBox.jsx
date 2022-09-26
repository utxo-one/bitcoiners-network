import PropTypes from 'prop-types';
import { Link } from "react-router-dom";
import Box from "../../layout/Box/Box";
import Button from "../../layout/Button/Button";
import ConnectionsChart from "../../layout/Connections/ConnectionsChart";

import './ConnectionsBox.scss';

const TYPES = {
  following: {
    phrase : 'Following',
    link   : '/following',
  },

  followers: {
    phrase : 'Followers',
    link   : '/followers'
  },
}

/**
 * @param {{
 *  connectionType: ('follower'|'followers'),
 *  user: Object
 * }} props 
 */
export default function ConnectionsBox({ connectionType, user }) {

  const connectionsCount = user && user[connectionType === 'followers' ? 'follower_data' : 'following_data'];

  return (
    <Box className="__connections-box">
      <div className="title">
        <div className="label">{ TYPES[connectionType].phrase }</div>
        <div className="count">{ Number(connectionsCount?.total).toLocaleString('en-US') }</div>
      </div>
      <hr />
      <ConnectionsChart connectionType={connectionType} user={user} />
      <Button as={Link} to={TYPES[connectionType].link}>View { TYPES[connectionType].phrase }</Button>
    </Box>
  );
}
