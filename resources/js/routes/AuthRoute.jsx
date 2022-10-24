import { useContext, useEffect } from "react";
import AppContext from "../store/AppContext";

export default function AuthRoute({ element }) {

  const [state] = useContext(AppContext);

  const { publicUser } = state;

  // If user is not logged in, redirect 
  useEffect(() => {
    if (publicUser) {
      window.location.href = '/';
    }
  }, [publicUser]);
  
  return element;
}
