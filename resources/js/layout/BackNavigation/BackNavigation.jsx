import { useLocation, useNavigate } from "react-router-dom";
import PointyArrow from "../../assets/icons/PointyArrow";

export default function BackNavigation({ fallbackRoute = '/' }) {

  const navigate = useNavigate();
  const { key } = useLocation();

  // when key is not 'default', the navigation has a 'back' route:
  const onClick = () => {
    navigate(key !== 'default' ? -1 : fallbackRoute);
  }

  return (
    <PointyArrow role="button" onClick={onClick} />
  )
}
