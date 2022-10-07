import { useEffect, useRef } from "react";
import Spinner from "./Spinner";
import classNames from "classnames";

import './InfiniteLoader.scss';

export default function InfiniteLoader({ onLoadMore, className, ...props }) {
  
  const infiniteLoaderRef = useRef();
  const infiniteLoaderObserver = useRef();

  const classes = classNames('__infinite-loader', className)

  useEffect(() => {
    const checkIntersection = entries => {
      entries.forEach(entry => entry.isIntersecting && onLoadMore());
    }

    infiniteLoaderObserver.current?.disconnect();

    if (infiniteLoaderRef.current) {
      infiniteLoaderObserver.current = new IntersectionObserver(checkIntersection);
      infiniteLoaderObserver.current.observe(infiniteLoaderRef.current);
    }
  }, []);



  return (
    <div className={classes} ref={infiniteLoaderRef} {...props}>
      <Spinner />
    </div>
  );
}
