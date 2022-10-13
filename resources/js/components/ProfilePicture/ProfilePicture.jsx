import axios from "axios";
import classNames from "classnames";
import { useEffect, useState } from "react"
import UserProfileImg from "../../assets/images/UserProfile.png";

import './ProfilePicture.scss';

export default function ProfilePicture({ user, className, ...props }) {

  const imageSrc = user?.twitter_profile_image_url_high_res;

  const [imageLoaded, setImageLoaded] = useState(false);
  const [imageError, setImageError] = useState(false);

  useEffect(() => {

    const loadImage = async () => {
      try {
        setImageLoaded(false);
        setImageError(false);

        const image = new Image();
        image.src = imageSrc;
        await image.decode();

        setImageLoaded(true);
      }
      catch {
        setImageError(true);
        axios.post(`/frontend/refresh/user/${user.twitter_username}`);
      }
    }

    imageSrc && loadImage();

  }, [imageSrc]);

  return (
    <div className={classNames("__profile-picture", className)}>
      { imageLoaded && <img src={imageSrc} /> }
      { imageError && <img src={UserProfileImg} /> }
    </div>
  )
}
