// credit ->
// https://github.com/facebook/react/issues/14099

// although also mentioned, this one seems to be slightly outdated:
// https://reactjs.org/docs/hooks-faq.html#how-to-read-an-often-changing-value-from-usecallback

// NOTE -> DO NOT call this in the render phase
/// NOTE #2 -> this may have unexpected effects in concurrent mode. For now, it works
import { useRef, useCallback, useLayoutEffect } from "react";

export default function useEventCallback(fn) {
  let ref = useRef();
  useLayoutEffect(() => {
    ref.current = fn;
  });
  return useCallback((...args) => ref.current.apply(void 0, args), []);
}
