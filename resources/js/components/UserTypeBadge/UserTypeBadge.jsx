import classNames from "classnames";
import PoopIcon from "../../assets/icons/PoopIcon";
import SadFaceIcon from "../../assets/icons/SadFaceIcon";
import BitcoinSymbolIcon from "../../assets/icons/BitcoinSymbolIcon";

import './UserTypeBadge.scss';

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

export default function UserTypeBadge({ userType, variant = 'outline', size = 'sm', ...props }) {

  if (!userType) {
    return null;
  }

  const renderIcon = () => {
    const Icon = USER_TYPES[userType].icon;
    return <Icon />
  }

  const badgeClasses = classNames(
    "__user-type-badge",
    `__user-type-badge-${userType}`,
    `__user-type-badge-${variant}`,
    `__user-type-badge-${size}`
  );

  return (
    <div className={badgeClasses} {...props}>
      <div className='icon'>{ renderIcon() }</div>
      <div className='label'>{ USER_TYPES[userType].phrase }</div>
    </div>
  )
}