import RadialBar from "./RadialBar";

import './ConnectionsChart.scss';
import classNames from "classnames";
import { calculatePercentages, CompactNumberFormat } from "../../utils/NumberFormatting";

const USER_TYPES = {
  bitcoiners: {
    phrase   : 'Bitcoiners',
    singular : 'bitcoiner',
  },

  shitcoiners: {
    phrase   : 'Shitcoiners',
    singular : 'shitcoiner',
  },

  nocoiners: {
    phrase   : 'Nocoiners',
    singular : 'nocoiner',
  },
}

export default function ConnectionsChart({ connectionType, user, showCount = true, onClickDiagram }) {
  
  const connectionData = connectionType === 'following' ? 'following_data' : 'follower_data';
  const userTypes = Object.keys(USER_TYPES);

  const percentages = user?.[connectionData] && calculatePercentages(user?.[connectionData], userTypes);

  const connectionsCount = user?.[`twitter_count_${connectionType}`] || 0;
  const analyzedConnections = user?.[connectionType === 'followers' ? 'follower_data' : 'following_data']?.total || 0;

  const renderAnalyzedConnections = () => {
    const percentage = analyzedConnections * 100 / connectionsCount;
    const percentageString = Math.ceil(percentage);
    const connectionsString = CompactNumberFormat(analyzedConnections, { digits: 3 });

    return (
      <div className='__connections-chart-count'>Analysis of <strong>{ connectionsString }</strong> ({percentageString === 100 ? 99 : percentageString}%) user's connections</div>
    );
  }

  return (
    <>
      { analyzedConnections < connectionsCount && renderAnalyzedConnections() }
      <div className="__connections-chart">
        { userTypes.map(type => (
          <div key={type} className={classNames("type", type)} onClick={() => onClickDiagram(USER_TYPES[type].singular)} role='button'>
            <div className="chart">
              <RadialBar className={type} percent={user?.[connectionData]?.total === 0 ? 0 : user?.[connectionData]?.[type] / user?.[connectionData]?.total * 100} />
              <div className="chart-percent">
                { percentages?.[type] }%
              </div>
            </div>
            { showCount && <div className="count">{ CompactNumberFormat(user?.[connectionData]?.[type]) }</div> }
            <div className="user-type">{ USER_TYPES[type].phrase }</div>
          </div>
        ))}
      </div>
    </>
  );
}
