import PropTypes from 'prop-types';
import { Link, useNavigate } from "react-router-dom";
import Box from "../../layout/Box/Box";
import Button from "../../layout/Button/Button";
import ConnectionsChart from "../../layout/Connections/ConnectionsChart";
import { CompactNumberFormat } from '../../utils/NumberFormatting';

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
export default function ConnectionsBox({ connectionType, user, isAuthUser, preventActions, ...props }) {

  const navigate = useNavigate();

  const connectionsCount = user?.[`twitter_count_${connectionType}`] || 0;

  const onClickDiagram = userType => {
    navigate(`/${connectionType}/${isAuthUser ? '' : user.twitter_username}`, { state: { initialUserType: userType } });
  }

  const connectionsPath = `${TYPES[connectionType].link}/${isAuthUser ? '' : user.twitter_username}`;

  return (
    <Box className="__connections-box" {...props}>
      <div className="title">
        <div className="label">{ TYPES[connectionType].phrase }</div>
        <div className="count">{ CompactNumberFormat(connectionsCount, {digits: 6 }) }</div>
      </div>
      <hr />
      <ConnectionsChart connectionType={connectionType} user={user} onClickDiagram={onClickDiagram} />
      <Button as={preventActions ? 'div' : Link} to={connectionsPath} variant="outline">View { TYPES[connectionType].phrase }</Button>
    </Box>
  );
}
