import * as DropdownMenu from "@radix-ui/react-dropdown-menu";
import classNames from "classnames";
import ArrowDownIcon from "../../assets/icons/ArrowDownIcon";
import { CompactNumberFormat } from "../../utils/NumberFormatting";

import './Connections.scss'; // using .connection-type button

const CONNECTION_TYPES = {
  following : 'Following',
  followers : 'Followers',
  available : 'Bitconers Network',
}

export default function ConnectionTypeDropdown({ connectionType, onSelect, count }) {

  const renderUserItems = () => (
    Object.entries(CONNECTION_TYPES).map(([type, phrase]) => (
      <DropdownMenu.Item
        key={type}
        className={classNames({ selected: connectionType === type })}
        onSelect={() => onSelect(type)}
      >
        { phrase }
      </DropdownMenu.Item>
    ))
  )

  return (
    <DropdownMenu.Root>
      <DropdownMenu.Trigger className="connection-type">
        <div>
          { CONNECTION_TYPES[connectionType] }{}
          {/* { count && <span className="user-count"> ({ CompactNumberFormat(count) })</span> } */}
        </div>
        <ArrowDownIcon />
      </DropdownMenu.Trigger>

      <DropdownMenu.Portal>
        <DropdownMenu.Content align="start" avoidCollisions={false} className="__dropdown-menu __connection-type-dropdown">
          <DropdownMenu.Label />
          { renderUserItems() }
        </DropdownMenu.Content>
      </DropdownMenu.Portal>
    </DropdownMenu.Root>
  )
}