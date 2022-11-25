import { useEffect, useState, useMemo } from "react"
import axios from "axios";
import classNames from "classnames";
import SadFaceIcon from "../../assets/icons/SadFaceIcon";
import UserProfileImg from "../../assets/images/UserProfile.png";

import './ProfilePicture.scss';

export default function ProfilePicture({ user, className, userNotFound, ...props }) {

  const [imageLoaded, setImageLoaded] = useState(false);
  const [imageError, setImageError] = useState(false);
  const [newImageSrc, setNewImgSrc] = useState(null);

  const imageSrc = useMemo(() => newImageSrc || user?.twitter_profile_image_url_high_res, [newImageSrc, user])

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
        const { data } = await axios.post(`/frontend/refresh/user/${user.twitter_username}`);
        setNewImgSrc(data?.twitter_profile_image_url_high_res);
      }
    }

    imageSrc && loadImage();

  }, [imageSrc]);

  return (
    <div className={classNames("__profile-picture", className)}>
      { userNotFound && <div className="not-found"><SadFaceIcon /></div> }
      { imageLoaded && <img src={imageSrc} /> }
      { imageError && <img src={UserProfileImg} /> }
    </div>
  )
}
