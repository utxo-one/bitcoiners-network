import classNames from "classnames";

import './Button.scss';

export default function Button({ as, className, children, variant, ...props }) {

  const Component = as ? as : 'button';

  const variantClass = variant ? `__button-variant-${variant}` : ''

  return (
    <Component className={classNames('__button', variantClass, className)} {...props}>
      { children }
    </Component>
  )
}