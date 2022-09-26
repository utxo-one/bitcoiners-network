import classNames from "classnames";

import './Box.scss';

export default function Box({ children, className, ...props }) {
  return (
    <div className={classNames('__box', className)} {...props}>
      { children }
    </div>
  );
}
