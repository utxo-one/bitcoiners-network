import { useEffect, useState } from "react";
import axios from "axios";
import { Link } from "react-router-dom";

export default function Dashboard() {

  const [pictures, setPictures] = useState([]);
  const [userData, serUserData] = useState(null);

  useEffect(() => {
    const loadProfilePictures = async () => {
      const { data } = await axios.get('/frontend/profile-pictures');
      setPictures(data);
    }

    const loadUserData = async () => {
      const { data } = await axios.get('frontend/user/auth');;

      console.log(data);
    }

    loadProfilePictures();
    loadUserData();
  }, []);

  return (
    <>
      <Link to='/followers'>Followers</Link>
      <Link to='/following'>Following</Link>
      
      <div style={{ display: 'flex', flexWrap: 'wrap', gap: 8 }}>
        { pictures.map(picture => (
          <img src={picture} style={{ borderRadius: 8 }} />
        ))}
      </div>
    </>
  );
}