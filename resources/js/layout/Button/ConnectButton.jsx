import classNames from "classnames";
import { useContext, useState } from "react";
import TopUpModal from "../../components/MassConnectModal/TopUpModal";
import AppContext from "../../store/AppContext";
import ButtonWithLightning from "./ButtonWithLightning";

import './Button.scss';

export default function ConnectButton({ connection, availableSats, onToggle, className, ...props }) {

  const [state, dispatch] = useContext(AppContext);

  const [loading, setLoading] = useState(false);
  const [showTopUp, setShowTopUp] = useState(false);

  const isFollowing = connection?.is_followed_by_authenticated_user;

  const { rates } = state;

  const connectAction = isFollowing ? 'unfollow' : 'follow';
  const connectionPrice = rates?.pricing[connectAction];

  const onClickButton = async () => {
    if (availableSats >= connectionPrice) {
      
      setLoading(true);
      const action = isFollowing ? 'delete' : 'post';
      const { data } = await axios[action](`/frontend/action/${connection.twitter_username}/${connectAction}`);
      dispatch({ type: 'balance/spend', payload: connectionPrice });
      setLoading(false);

      onToggle?.();
    }
    else {
      setShowTopUp(true);
    }
  }

  const classes = classNames("__button-connect", className, `__button-connect-${connection?.type}`);
  
  return (
    <>
      <ButtonWithLightning onClick={onClickButton} loading={loading} className={classes}>
        { isFollowing ? 'Unfollow' : 'Follow' }
      </ButtonWithLightning>
      <TopUpModal show={showTopUp} onHide={() => setShowTopUp(false)} message='top-up-required' />
    </>
  );
}
