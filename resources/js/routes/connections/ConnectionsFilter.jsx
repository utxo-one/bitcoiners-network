import * as DropdownMenu from "@radix-ui/react-dropdown-menu";
import classNames from "classnames";

import './Connections.scss'; // using .filter button
import './ConnectionsFilter.scss';

const USER_TYPE_FILTERS = {
  all        : 'All Users',
  bitcoiner  : 'Bitcoiners',
  shitcoiner : 'Shitcoiners',
  nocoiner   : 'Nocoiners'
}

export default function ConnectionsFilter({ userType, connectionType, onSelectUserType, disabled }) {

  const renderUserItems = () => (
    Object.entries(USER_TYPE_FILTERS).filter(([type]) => !(connectionType === 'available' && type === 'all')).map(([type, phrase]) => (
      <DropdownMenu.Item
        key={type}
        className={classNames({ selected: userType === type, [type]: userType === type })}
        onSelect={() => onSelectUserType(type)}
      >
        { phrase }
      </DropdownMenu.Item>
    ))
  )

  return (
    <DropdownMenu.Root>
      <DropdownMenu.Trigger className="filter" disabled={disabled}>Filter</DropdownMenu.Trigger>

      <DropdownMenu.Portal>
        <DropdownMenu.Content align="end" avoidCollisions={false} className="__dropdown-menu __connections-filter" >
          <DropdownMenu.Label />
          { renderUserItems() }
        </DropdownMenu.Content>
      </DropdownMenu.Portal>
    </DropdownMenu.Root>
  )
}