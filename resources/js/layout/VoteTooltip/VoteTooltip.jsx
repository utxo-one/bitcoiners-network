import axios from 'axios';
import classNames from 'classnames';
import { useContext } from 'react';
import AppContext from '../../store/AppContext';
import './VoteTooltip.scss';

export default function VoteTooltip({ arrowDirection = 'down' }) {

  const [state, dispatch] = useContext(AppContext);

  const { currentUser } = state;

  const closeTooltip = () => {
    axios.post('/frontend/close-classification-tip');
    dispatch({ type: 'currentUser/close-vote-tip'});
  }

  if (!currentUser || currentUser.closed_vote_tip) {
    return null;
  }

  return (
    <div className={classNames("__vote-tooltip", `__vote-tooltip-arrow-${arrowDirection}`)}>
      <div>Vote</div>
      <div className="close" onClick={closeTooltip}>×</div>
    </div>
  );
}
