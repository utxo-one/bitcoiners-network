import classNames from "classnames";
import PoopIcon from "../../assets/icons/PoopIcon";
import SadFaceIcon from "../../assets/icons/SadFaceIcon";
import BitcoinSymbolIcon from "../../assets/icons/BitcoinSymbolIcon";

import './UserTypeLabel.scss';

const USER_TYPES = {
  bitcoiner: {
    phrase: "Bitcoiner",
    icon: BitcoinSymbolIcon,
  },

  shitcoiner: {
    phrase: "Shitcoiner",
    icon: PoopIcon,
  },

  nocoiner: {
    phrase: "Nocoiner",
    icon: SadFaceIcon,
  },
}

export default function UserTypeLabel({ userType, variant='solid' }) {
  
  if (!userType) {
    return null;
  }

  const renderIcon = () => {
    const Icon = USER_TYPES[userType].icon;
    return <Icon />
  }

  return (
    <div className={classNames("__user-type-label", `__user-type-label-${variant}`, `__user-type-label-${userType}`)}>
      { renderIcon() }
      <div className='label'>{ USER_TYPES[userType].phrase }</div>
    </div>
  )
}