import { useEffect, useState } from "react"
import { useNavigate } from "react-router-dom";
import { useImmer } from "use-immer";
import axios from "axios";
import classNames from "classnames";

import CampaignStats from "../../components/CampaignStats/CampaignStats";
import CampaignUsers from "./CampaignUsers";
import CancelCampaignModal from "./CancelCampaignModal";

import PointyArrow from "../../assets/icons/PointyArrow";
import Box from "../../layout/Box/Box";
import Button from "../../layout/Button/Button";
import CenteredSpinner from "../../layout/Spinner/CenteredSpinner";

import './CampaignOverview.scss';

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
  const [pendingData, setPendingData] = useImmer(null);
  const [selectedPending, setSelectedPending] = useImmer(() => new Map());
  const [showCancelCampaign, setShowCancelCampaign] = useState(false);
  const [pendingUsers, setPendingUsers] = useImmer(null);
  const [cancellingRequests, setCancellingRequests] = useState(false);
  const [availableSats, setAvailableSats] = useState(0);

  const navigate = useNavigate();

  const loadedAllPending = pendingData && pendingData.current_page === pendingData.last_page;

  useEffect(() => {
    const loadCampaign = async () => {
      const { data } = await axios.get('/frontend/follow/mass-follow');
      const { data: pending } = await axios.get('/frontend/follow/requests/pending');
      const { data: available } = await axios.get('/frontend/user/available-balance');
      setAvailableSats(available || 0);

      setCampaignData(data);
      setPendingData(pending);
      setPendingUsers(pending.data);
    }

    loadCampaign();
  }, []);

  const goBack = () => {
    navigate(-1);
  }

  const onToggleSelected = (e, user) => {
    setSelectedPending(draft => {
      if (e.target.checked) {
        draft.set(user.twitter_id, true);
        // draft[user.twitter_id] = true;
      }
      else {
        draft.delete(user.twitter_id);
      }
    });
  }

  const onCancelPendingRequests = async () => {
    console.log('delete:', selectedPending.keys());

    setCancellingRequests(true);
    const { data } = axios.delete('/frontend/follow/requests', { data: { twitterIds: Array.from(selectedPending.keys()) } });

    console.log('data:', data)

    // TODO -> replace array lookup with object
    setPendingUsers(draft => {
      selectedPending.forEach((_, id) => {
        const index = draft.findIndex(user => user.follow.twitter_id === id);
        index !== -1 && draft.splice(index, 1);
      })
    });

    // reset pending:
    setCancellingRequests(false);
    setSelectedPending(new Map());
  }

  const showCancelRequestsButton = selectedTab === 'audience' && selectedPending.size > 0;

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

      <CampaignUsers pendingUsers={pendingUsers} loadedAllPending={loadedAllPending} selected={selectedPending} onToggleSelected={onToggleSelected} />
    </>
  );

  const renderCampaignContent = () => {
    if (!campaignData) {
      return <CenteredSpinner />
    }

    return (
      <>
        <main className={classNames({ 'cancel-button-visible': showCancelRequestsButton })}>
          { selectedTab === 'overview' ? renderOverview() : renderAudience() }
        </main>

        { showCancelRequestsButton && (
          <div className="cancel-requests">
            <Button onClick={onCancelPendingRequests} loading={cancellingRequests} disabled={cancellingRequests}>Cancel Requests</Button>
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
      <CancelCampaignModal show={showCancelCampaign} onHide={() => setShowCancelCampaign(false)} availableSats={availableSats} />
    </div>
  )
}
