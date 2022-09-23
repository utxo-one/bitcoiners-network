import axios from "axios";
import { useEffect, useState } from "react";
import Button from "./layout/Button";

export default function ReactApp() {

  const [pictures, setPictures] = useState([]);

  useEffect(() => {
    const loadProfilePictures = async () => {
      const { data } = await axios.get('/frontend/profile-pictures');

      setPictures(data);

      console.log(data);
    }

    loadProfilePictures();
  }, []);

  return (
    <>
      <div>Hi, here are some pictures from bitcoiners</div>
      <div style={{ display: 'flex', flexWrap: 'wrap', gap: 8 }}>
        { pictures.map(picture => (
          <img src={picture} style={{ borderRadius: 8 }} />
        ))}
        <Button>React Button</Button>
      </div>
    </>
  );
}