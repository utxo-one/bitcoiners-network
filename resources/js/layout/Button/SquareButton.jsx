import classNames from 'classnames';
import Button from './Button';

import './Button.scss';

export default function SquareButton({ children, className, icon, selected, ...props }) {

  return (
    <Button className={classNames("__button-square", className, {["__button-selected"]: selected })} {...props}>
      { icon && <div className='icon'>{ icon }</div> }
      <div>{ children }</div>
    </Button>
  );
}
