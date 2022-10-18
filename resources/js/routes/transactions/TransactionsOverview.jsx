import classNames from "classnames";
import { useContext, useEffect, useMemo, useState } from "react";
import { useSearchParams } from "react-router-dom";
import { useImmer } from "use-immer";
import TopUpModal from "../../components/MassConnectModal/TopUpModal";
import useEventCallback from "../../hooks/useEventCallback";
import Box from "../../layout/Box/Box";
import ButtonWithLightning from "../../layout/Button/ButtonWithLightning";
import HamburgerMenu from "../../layout/HamburgerMenu/HamburgerMenu";
import CenteredSpinner from "../../layout/Spinner/CenteredSpinner";
import InfiniteLoader from "../../layout/Spinner/InfiniteLoader";
import AppContext from "../../store/AppContext";
import { CompactNumberFormat } from "../../utils/NumberFormatting";
import TopUpSuccessModal from "./TopUpSuccessModal";
import Cookies from 'js-cookie';

import './TransactionsOverview.scss';

const TRANSACTION_TABS = {
  credits: 'Credits',
  spending: 'Spending',
};

export default function TransactionsOverview(props) {

  const [state, dispatch] = useContext(AppContext);

  const [searchParams] = useSearchParams();

  const [selectedTab, setSelectedTab] = useState('credits');
  const [depositsResponse, setDepositsResponse] = useState();
  const [deposits, setDeposits] = useImmer();
  const [debitsResponse, setDebitsResponse] = useState();
  const [debits, setDebits] = useImmer();
  const [showTopUp, setShowTopUp] = useState(false);
  const [loadingDeposits, setLoadingDeposits] = useState(false);
  const [loadingDebits, setLoadingDebits] = useState(false);
  const [showTopUpSuccess, setShowTopUpSuccess] = useState(false);

  const verifiedBitcoiner = useMemo(() => {
    return searchParams.get('verified_bitcoiner') === 'true';
  }, [searchParams]);

  const { availableSats } = state;

  useEffect(() => {

    // Keep in session storage (IE: while browser is open) the previous top up times that have been shown already,
    // to prevent the user from pressing back or going to a route with time and seing the modal:
    const topUpTime = searchParams.get('top_up_time');
    const cookie = Cookies.get("__bn__top_up_times");
    const shownTimes = cookie?.split(',') || [];

    if (topUpTime && !shownTimes.includes(topUpTime)) {
      setShowTopUpSuccess(true);
      Cookies.set("__bn__top_up_times", `${cookie ? cookie + ',' : ''}${topUpTime}`);
    }
  }, [searchParams]);

  const loadedAllDebits = debitsResponse && debitsResponse.current_page === debitsResponse.last_page;
  const loadedAllDeposits = depositsResponse && depositsResponse.current_page === depositsResponse.last_page;

  // Scroll whenever the selected tab changes
  useEffect(() => {
    document.documentElement.scrollTo({ top: 0, left: 0, behavior: "instant" });
  }, [selectedTab]);

  useEffect(() => {
    const loadDeposits = async () => {
      const { data: responseData } = await axios.get('/frontend/transaction/deposit');
      setDepositsResponse(responseData);
      setDeposits(responseData.data);
    }

    const loadDebits = async () => {
      const { data: responseData } = await axios.get('/frontend/transaction/debit');
      setDebitsResponse(responseData);
      setDebits(responseData.data);
    }

    loadDeposits();
    loadDebits();
  }, []);

  const onLoadMoreDeposits = useEventCallback(async () => {
    if (loadingDeposits) {
      return;
    }

    setLoadingDeposits(true);
    
    const page = depositsResponse.current_page + 1;
    const { data: responseData } = await axios.get(`/frontend/transaction/deposit?page=${page}`);
    
    setDepositsResponse(responseData);
    setDeposits(draft => {
      draft.push(...responseData.data)
    });
    setLoadingDeposits(false);
  });

  const onLoadMoreDebits = useEventCallback(async () => {
    if (loadingDebits) {
      return;
    }

    setLoadingDebits(true);
    
    const page = debitsResponse.current_page + 1;
    const { data: responseData } = await axios.get(`/frontend/transaction/debit?page=${page}`);

    setDebitsResponse(responseData);
    setDebits(draft => {
      draft.push(...responseData.data)
    });
    setLoadingDebits(false);
  });

  const renderCreditsOverview = () => (
    <>
      <Box>
        <h3>Available Balance</h3>
        <hr />
        <div className="sats"><strong>{ CompactNumberFormat(availableSats, { digits: 12 }) }</strong> Sats</div>
      </Box>

      { deposits.map(deposit => (
        <div key={deposit.id} className="transaction">
          <div>
            <div className="description">Top Up Via Lightning</div>
            <div className="date">{ new Date(deposit.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }</div>
          </div>
          <div>
            <div className="amount">{ CompactNumberFormat(deposit.amount, { digits: 12 }) }</div>
            <div className="currency">Sats</div>
          </div>
        </div>
      ))}

      { deposits && !loadedAllDeposits && <InfiniteLoader onLoadMore={onLoadMoreDeposits} /> }
    </>
  )

  const renderDebitsOverview = () => (
    <>
      { debits.map(debit => (
        <div key={debit.id} className="transaction">
          <div>
            <div className="description">{ debit.description }</div>
            <div className="date">{ new Date(debit.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }</div>
          </div>
          <div>
            <div className="amount">−{ CompactNumberFormat(debit.amount, { digits: 12 }) }</div>
            <div className="currency">Sats</div>
          </div>
        </div>
      ))}

      { debits && !loadedAllDebits && <InfiniteLoader onLoadMore={onLoadMoreDebits} /> }
    </>
  )

  const renderContent = () => {
    if (!depositsResponse || typeof availableSats !== 'number') {
      return <CenteredSpinner />
    }

    return (
      <>
        <main>
          { selectedTab === 'credits' ? renderCreditsOverview() : renderDebitsOverview() }
        </main>

        <div className="bottom-top-up">
          <ButtonWithLightning onClick={() => setShowTopUp(true)}>Top Up Via Lightning</ButtonWithLightning>
        </div>
      </>
    );
  }

  return (
    <div className="__transactions-overview">
      <header>
        <HamburgerMenu variant='inverted' />
        { Object.entries(TRANSACTION_TABS).map(([tab, phrase]) => (
          <div key={tab} role="button" className={classNames("tab", { selected: selectedTab === tab})} onClick={() => setSelectedTab(tab)}>{ phrase }</div>
        ))}
      </header>
      { renderContent() }
      <TopUpModal show={showTopUp} onHide={() => setShowTopUp(false)} />
      <TopUpSuccessModal show={showTopUpSuccess} onHide={() => setShowTopUpSuccess(false)} verifiedBitcoiner={verifiedBitcoiner} />
    </div>
  );
}
