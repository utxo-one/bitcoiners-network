import classNames from 'classnames';
import Button from './Button';
import BoltIcon from "../../assets/icons/BoltIcon";

import './Button.scss';
import TwitterIcon from '../../assets/icons/TwitterIcon';

export default function TwitterButton({ children, className, ...props }) {
  return (
    <Button className={classNames("__button-twitter", className)} {...props}>
      <TwitterIcon /><div>{ children }</div>
    </Button>
  );
}