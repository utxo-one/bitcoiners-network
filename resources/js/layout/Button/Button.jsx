import classNames from "classnames";

import './Button.scss';

export default function Button({ as, className, children, ...props }) {

  const Component = as ? as : 'button';

  return (
    <Component className={classNames('__button', className)} {...props}>
      { children }
    </Component>
  )
}