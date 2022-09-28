import classNames from 'classnames';
import Button from './Button';
import BoltIcon from "../../assets/icons/BoltIcon";

import './Button.scss';

export default function ButtonWithLightning({ children, className, ...props }) {
  return (
    <Button className={classNames("__button-with-lightning", className)} {...props}>
      <BoltIcon /><div>{ children }</div>
    </Button>
  );
}