import classNames from "classnames";
import { useEffect, useState } from "react"
import UserProfileImg from "../../assets/images/UserProfile.png";

import './ProfilePicture.scss';

export default function ProfilePicture({ user, className, ...props }) {

  const imageSrc = user?.twitter_profile_image_url;

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
        console.log("BE CALL -> Image could not be loaded for user_id:", user.twitter_id);
        setImageError(true);
      }
    }

    imageSrc && loadImage();

  }, [imageSrc])

  return (
    <div className={classNames("__profile-picture", className)}>
      { imageLoaded && <img src={imageSrc} /> }
      { imageError && <img src={UserProfileImg} /> }
    </div>
  )
}
