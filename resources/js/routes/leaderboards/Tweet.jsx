import ProfilePicture from '../../components/ProfilePicture/ProfilePicture';
import { SAMPLE_USER } from './sampleUser';
import './Tweet.scss';

export default function Tweet({ tweet }) {

  // console.log('tweet:', tweet);

  const includes = JSON.parse(tweet.includes);
  const entities = JSON.parse(tweet.entities);

  const user = SAMPLE_USER;

  // console.log('includes:', includes)
  let videoUrl;
  let imageUrl;
  let website;
  let mediaType;

  if (['video', 'animated_gif'].includes(includes?.media?.[0]?.type)) {
    videoUrl = includes?.media?.[0]?.variants?.[1]?.url || includes?.media?.[0]?.variants?.[0]?.url;

    mediaType = includes?.media?.[0]?.type;

    // test vertical video
    // videoUrl = "http://www.exit109.com/~dnn/clips/RW20seconds_1.mp4";
  
    //this?.paused ? this?.play() : this?.pause()
  }

  else if (includes?.media?.[0]?.type === 'photo') {
    imageUrl = includes.media[0].url;
  }

  else {
    // console.log('entities:', entities)

    website = {};

    website.url = entities.urls[0]?.expanded_url;

    if (website.url) {
      let domain = (new URL(website.url));
      website.domain = domain.hostname.replace('www.','');
    }

    website.title = entities.urls[0]?.title;
    website.image = entities.urls[0]?.images?.[0]?.url;

    // console.log('website:', website)
  }

  const animatedGif = mediaType === 'animated_gif';
  

  return (
    <div className="__tweet">
      <div className='__tweet-user'>
        <ProfilePicture user={user} />
        <div className='__tweet-user-info'>
          <div className='__tweet-user-name'>{ user.name }</div>
          <div className='__tweet-user-handle'>@{ user.twitter_username }</div>
        </div>
        <div className='__tweet-user-options'>...</div>
      </div>

      <div className="__tweet-text">
        { tweet.text }
      </div>

      { videoUrl && (
        <div className="__tweet-video">
          <video onClick={e => console.log(e)} controls={!animatedGif} loop={animatedGif} muted={animatedGif} autoPlay={animatedGif} playsInline={animatedGif}>
            <source type="video/mp4" src={videoUrl} />
          </video>
        </div>
      )}

      { imageUrl && (
        <div className="__tweet-image">
          <img src={imageUrl} />
        </div>
      )}

      { website && (
        <a className="__tweet-website-card" src={website.url}>
          <img src={website.image} />
          <div className='domain'>{ website.domain }</div>
          <div className='title'> { website.title }</div>
        </a>
      )}

      <div className="__tweet-stats">
        <div><strong>{ tweet.likes }</strong> likes</div>
        <div><strong>{ tweet.retweets }</strong> retweets</div>
        <div><strong>{ tweet.replies }</strong> replies</div>
      </div>
    </div>
  );
}
