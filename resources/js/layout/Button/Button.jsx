import classNames from "classnames";
import Spinner from "../Spinner/Spinner";

import './Button.scss';

export default function Button({ as, className, children, variant, loading, ...props }) {

  const Component = as ? as : 'button';

  const variantClass = variant ? `__button-variant-${variant}` : ''

  return (
    <Component className={classNames('__button', variantClass, className)} {...props}>
      <div className={classNames("__button-spinner", {"__button-spinner-visible": loading })}><Spinner /></div>
      <div className={classNames("__button-content", {'__button-content-invisible': loading })}>
        { children }
      </div>
    </Component>
  )
}