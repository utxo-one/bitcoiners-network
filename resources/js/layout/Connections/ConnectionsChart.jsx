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

  const percentages = user && calculatePercentages(user?.[connectionData], userTypes);

  return (
    <div className="__connections-chart">
      { userTypes.map(type => (
        <div key={type} className={classNames("type", type)} onClick={() => onClickDiagram(USER_TYPES[type].singular)}>
          <div className="chart">
            <RadialBar className={type} percent={user?.[connectionData].total === 0 ? 0 : user?.[connectionData][type] / user?.[connectionData].total * 100} />
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
