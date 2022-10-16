import PlayIcon from '../../assets/icons/PlayIcon';
import { CompactNumberFormat } from '../../utils/NumberFormatting';
import './CampaignStats.scss';

export default function CampaignStats({ campaign }) {

  const renderCampaignStatus = () => {
    switch (campaign.status) {
      case 'running':
        return <div className="status-running"><PlayIcon /> <span>Running</span></div>

      case 'paused':
        return <div>Paused</div>

      default:
        return null;  
    }
  }

  return (
    <table className="__campaign-stats">
      <tbody>
        <tr>
          <td>Status</td>
          <td>{ renderCampaignStatus() }</td>
        </tr>

        <tr>
          <td>Completion Time</td>
          <td><strong>{Math.ceil(campaign.estimatedCompletionDays)}</strong> days left</td>
        </tr>

        <tr>
          <td>Accounts Followed</td>
          <td><strong>{ CompactNumberFormat(campaign.totalCompletedFollowRequests, { digits: 6 })}</strong> Users</td>
        </tr>

        <tr>
          <td>Amount Spent</td>
          <td><strong>{ CompactNumberFormat(campaign.totalSpentSats, { digits: 7 })}</strong> Sats</td>
        </tr>

        <tr>
          <td>Pending Requests</td>
          <td><strong>{ CompactNumberFormat(campaign.pendingFollowRequests, { digits: 6 })}</strong> Users</td>
        </tr>
      </tbody>
    </table>
  );
}
