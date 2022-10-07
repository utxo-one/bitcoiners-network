import axios from "axios";
import classNames from "classnames";
import { useEffect, useState } from "react"
import { useNavigate } from "react-router-dom";
import PointyArrow from "../../assets/icons/PointyArrow";
import CampaignStats from "../../components/CampaignStats/CampaignStats";
import Box from "../../layout/Box/Box";
import Button from "../../layout/Button/Button";
import CenteredSpinner from "../../layout/Spinner/CenteredSpinner";

import './CampaignOverview.scss';
import CampaignUsers from "./CampaignUsers";
import CancelCampaignModal from "./CancelCampaignModal";

const CAMPAIGN_STATUS = {
  running: "Running",
  neverStarted: "Hodor"
};

const CAMPAIGN_TABS = {
  overview: 'Overview',
  audience: 'Audience',
};

export default function CampaignOverview(props) {

  const [selectedTab, setSelectedTab] = useState('overview');
  const [campaignData, setCampaignData] = useState(null);
  const [pendingData, setPendingData] = useState(null);
  const [selectedPending, setSelectedPending] = useState({});
  const [showCancelCampaign, setShowCancelCampaign] = useState(false);

  const navigate = useNavigate();

  useEffect(() => {
    const loadCampaign = async () => {
      const { data } = await axios.get('/frontend/follow/mass-follow');
      const { data: pending } = await axios.get('/frontend/follow/requests/pending');

      setCampaignData(data);
      setPendingData(pending);

      console.log('pending:', pending)
    }

    loadCampaign();
  }, []);

  const goBack = () => {
    navigate(-1);
  }

  const onToggleSelected = (e, user) => {
    const selected = JSON.parse(JSON.stringify(selectedPending));
    
    if (e.target.checked) {
      selected[user.twitter_id] = true;
    }
    else {
      delete selected[user.twitter_id];
    }

    setSelectedPending(selected);
  }

  const renderOverview = () => (
    <>
      <CampaignStats campaign={campaignData} />
      <Button className='cancel-campaign' onClick={() => setShowCancelCampaign(true)}>Cancel Campaign</Button>
        
      { campaignData.recentCompletedFollows?.length > 0 && (
        <Box className='followed-accounts'>
          <h3>Recently followed accounts</h3>
          <hr />

          <CampaignUsers campaign={campaignData} />
        </Box>
      )}
    </>
  );

  const renderAudience = () => (
    <>
      <Box variant='info' className='cancel-info'>
        <p>Select one or multiple accounts to cancel the request on this campaign.</p>
        <p>Accounts that you choose to cancel will not be charged nor chosen for future campaigns.</p>
      </Box>

      <CampaignUsers users={pendingData.data} selected={selectedPending} onToggleSelected={onToggleSelected} />
    </>
  );

  const renderCampaignContent = () => {
    if (!campaignData) {
      return <CenteredSpinner />
    }

    return (
      <>
        <main>
          { selectedTab === 'overview' ? renderOverview() : renderAudience() }
        </main>

        { selectedTab === 'audience' && Object.keys(selectedPending).length > 0 && (
          <div className="cancel-requests">
            <Button>Cancel Requests</Button>
          </div>
        )}
      </>
    );
  }

  return (
    <div className="__campaign-overview">
      <header>
        <PointyArrow role="button" className="back" onClick={goBack} />
        { Object.entries(CAMPAIGN_TABS).map(([tab, phrase]) => (
          <div key={tab} className={classNames("tab", { selected: selectedTab === tab})} onClick={() => setSelectedTab(tab)}>{ phrase }</div>
        ))}
      </header>

      { renderCampaignContent() }
      <CancelCampaignModal show={showCancelCampaign} onHide={() => setShowCancelCampaign(false)} />
    </div>
  )
}
