import classNames from "classnames";
import { useState } from "react";
import TopUpModal from "../../components/MassConnectModal/TopUpModal";
import ButtonWithLightning from "./ButtonWithLightning";

export default function ConnectButton({ connection, availableSats, onToggle, className, ...props }) {

  const [loading, setLoading] = useState(false);
  const [showTopUp, setShowTopUp] = useState(false);

  const isFollowing = connection?.is_followed_by_authenticated_user;

  const onClickButton = async () => {
    if (availableSats >= 100) {
      
      setLoading(true);
      const action = isFollowing ? 'delete' : 'post';
      const routeType = isFollowing ? 'unfollow' : 'follow';
      const { data } = await axios[action](`/frontend/action/${connection.twitter_username}/${routeType}`);
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
      <TopUpModal show={showTopUp} onHide={() => setShowTopUp(false)} />
    </>
  );
}
