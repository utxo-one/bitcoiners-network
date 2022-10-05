import classNames from "classnames";
import { useEffect, useState } from "react"
import { useNavigate } from "react-router-dom";
import PlayIcon from "../../assets/icons/PlayIcon";
import PointyArrow from "../../assets/icons/PointyArrow";
import Button from "../../layout/Button/Button";
import { CompactNumberFormat } from "../../utils/NumberFormatting";

import './CampaignOverview.scss';

const CAMPAIGN_STATUS = {
  running: "Running",
  neverStarted: "Hodor"
};

export default function CampaignOverview(props) {

  const [selectedTab, setSelectedTab] = useState('overview');
  const [campaignData, setCampaignData] = useState();
  const navigate = useNavigate();

  useEffect(() => {
    const loadCampaign = async () => {
      const { data } = await axios.get('/frontend/follow/mass-follow');

      console.log('data:', data);
      setCampaignData(data);
    }

    loadCampaign();
  }, []);

  const goBack = () => {
    navigate(-1);
  }

  if (!campaignData) {
    return <pre>[ no data \ loading ]</pre>
  }

  const renderCampaignStatus = () => {
    switch (campaignData.status) {
      case 'running':
        return <div className="status-running"><PlayIcon /> <span>Running</span></div>

      default:
        return null;  
    }
  }

  return (
    <div className="__campaign-overview">
      <header>
        <PointyArrow role="button" className="back" onClick={goBack} />
        <div className={classNames("tab", { selected: selectedTab === 'overview'})}>Overview</div>
        <div className={classNames("tab", { selected: selectedTab === 'audience'})}>Audience</div>
      </header>

      <main>
        <table className="overview">
          <tbody>
            <tr>
              <td>Status</td>
              <td>{ renderCampaignStatus() }</td>
            </tr>

            <tr>
              <td>Completion Time</td>
              <td><strong>{Math.ceil(campaignData.estimatedCompletionDays)}</strong> days left</td>
            </tr>

            <tr>
              <td>Accounts Followed</td>
              <td><strong>{ CompactNumberFormat(campaignData.totalCompletedFollowRequests, { digits: 6 })}</strong> Users</td>
            </tr>

            <tr>
              <td>Amount Spent</td>
              <td><strong>{ CompactNumberFormat(campaignData.totalSpentSats, { digits: 7 })}</strong> Sats</td>
            </tr>
          </tbody>
        </table>

        <Button className='cancel-campaign'>Cancel Campaign</Button>
      </main>
    </div>
  )
}
