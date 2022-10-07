import classNames from "classnames";

import './Box.scss';

export default function Box({ children, variant, className, ...props }) {

  const variantClass = variant ? `__box-variant-${variant}` : '';

  return (
    <div className={classNames('__box', variantClass, className)} {...props}>
      { children }
    </div>
  );
}
