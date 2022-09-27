import RadialBar from "./RadialBar";

import './ConnectionsChart.scss';
import classNames from "classnames";
import { CompactNumberFormat } from "../../utils/NumberFormatting";

const USER_TYPES = {
  bitcoiners: {
    phrase: 'Bitcoiners',
  },

  shitcoiners: {
    phrase: 'Shitcoiners',
  },

  nocoiners: {
    phrase: 'Nocoiners',
  },
}

export default function ConnectionsChart({ connectionType, user, showCount = true }) {

  console.log('user:', user)

  const userTypes = Object.keys(USER_TYPES);
  const connectionData = connectionType === 'following' ? 'following_data' : 'follower_data';

  const calculatePercentages = connections => {
    let maxPercentage = -1;
    let maxPercentageType;
    let sum = 0;
    let percentages = {};
    
    userTypes.forEach(type => {
      const percentage = Math.round(connections[type] / connections.total * 100);
      sum += percentage;
      
      if (percentage > maxPercentage) {
        maxPercentageType = type;
        maxPercentage = percentage;
      }

      percentages[type] = percentage;
    });

    if (sum > 100) {
      percentages[maxPercentageType] -= (sum - 100);
    }

    return (percentages); 
  }

  const percentages = user && calculatePercentages(user?.[connectionData]);

  if (user?.[connectionData].total === 0) {
    return (
      <pre>[ THIS USER HAS NO CONNECTIONS ]</pre>
    )
  }

  return (
    <div className='__connections-chart'>
      { userTypes.map(type => (
        <div key={type} className={classNames("type", type)}>
          <div className="chart">
            <RadialBar className={type} percent={user?.[connectionData][type] / user?.[connectionData].total * 100} />
            <div className="chart-percent">
              { percentages[type] }%
            </div>
          </div>
          { showCount && <div className="count">{ CompactNumberFormat(user?.[connectionData][type]) }</div> }
          <div className="user-type">{ USER_TYPES[type].phrase }</div>
        </div>
      ))}
    </div>
  );
}
