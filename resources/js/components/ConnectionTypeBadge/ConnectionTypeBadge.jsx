import './ConnectionTypeBadge.scss';

export default function ConnectionTypeBadge({ connection, type, followsYou = false, following = false}) {

  if (type === 'follows-you' && connection?.follows_authenticated_user || followsYou) {
    return <div className="__connection-type-badge">Follows You</div>
  }

  else if (type === 'following' && connection?.is_followed_by_authenticated_user || following) {
    return <div className="__connection-type-badge">Following</div>
  }

  return null;
}
