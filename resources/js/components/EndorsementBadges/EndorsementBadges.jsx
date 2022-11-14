import { useMemo } from "react"
import classNames from "classnames";
import { CompactNumberFormat } from "../../utils/NumberFormatting";
import { ENDORSEMENT_TYPES } from "../../utils/Types";

import EndorseIcon from "../../assets/icons/EndorseIcon";
import './EndorsementBadges.scss';

const MAX_VISIBLE_MOBILE = 2;
const MAX_VISIBLE_BADGES = 3;

export default function EndorsementBadges({ user, onClick, viewingOwnProfile }) {

  const { _endorsements: endorsements } = user || {};

  const visibleEndorsements = useMemo(() => (
    endorsements ? Object.entries(endorsements).filter(([_, count]) => count > 0) : null
  ), [endorsements]);

  if (!user || !endorsements) {
    return (
      <div className="__endorsement-badges">
        <div className="skeleton-badge" />
        <div className="skeleton-badge" />
      </div>
    );
  }

  const hasEndorsements = Object.values(endorsements).some(count => count > 0);
  
  const renderAdd = device => {
    const max = device === 'mobile' ? MAX_VISIBLE_MOBILE : MAX_VISIBLE_BADGES;

    if (visibleEndorsements.length <= max) {
      return (
        viewingOwnProfile
        ? null
        : <button className={classNames("add", device)} onClick={onClick}><span className="plus">+</span> Add</button>
      );
    }

    else {
      return (
        <div className={classNames("x-more", device)} role="button" onClick={onClick}>
          {'...'}{visibleEndorsements.length - max} more
        </div>
      );
    }
  }

  if (!hasEndorsements) {
    return (
      <div className="__endorsement-badges __endorsement-badges-none">
        <div className="text-icon">
          <EndorseIcon />
          No skill endorsements yet
        </div>
        { !viewingOwnProfile && <button onClick={onClick}><span className="plus">+</span> Add</button> }
      </div>
    );
  }

  return (
    <div className="__endorsement-badges">
      { visibleEndorsements.slice(0, MAX_VISIBLE_BADGES).map(([type, count], index) => (
        <div key={type} className='endorsement-row' role='button' onClick={onClick}>
          <div key={type} className={classNames('badge', `badge-${ENDORSEMENT_TYPES[type].color}`, {'extra-badge': index === MAX_VISIBLE_MOBILE })}>
            <div className="text">{ ENDORSEMENT_TYPES[type].phrase.one }</div>
            { count > 0 && <span className="count">{ CompactNumberFormat(count, { digits: 3 }) }</span> }
          </div>
        </div>
      ))}

      { renderAdd('mobile') }
      { renderAdd('desktop') }
  </div>
  )
}
