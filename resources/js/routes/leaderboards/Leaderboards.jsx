import axios from "axios";
import { useState } from "react";
import { useEffect } from "react";
import { useImmer } from "use-immer";
import { ENDORSEMENT_TYPES } from "../../utils/Types";
import classNames from "classnames";
import * as DropdownMenu from "@radix-ui/react-dropdown-menu";

import BitcoinersRanking from "./BitcoinersRanking";
import Markets from "./Markets";

import BitcoinersNetworkLogo from "../../assets/icons/BitcoinersNetworkLogo";
import UserIcon from "../../assets/icons/UserIcon";
import AstrologyIcon from "../../assets/icons/AstrologyIcon";
import ChatIcon from "../../assets/icons/ChatIcon";
import SquareButton from "../../layout/Button/SquareButton";
import ArrowDownIcon from "../../assets/icons/ArrowDownIcon";
import SearchBar from "./SearchBar";
import SearchIcon from "../../assets/icons/SearchIcon";

import './Leaderboards.scss';
import UserInfoPanel from "../../components/UserInfoPanel/UserInfoPanel";
import useEndorsements from "../../hooks/useEndorsements";
import EndorsementModal from "../connections/EndorsementModal";
import CommunityRateModal from "../connections/CommunityRateModal";

const BITCOINER_TABS = {
  titans  : { content: 'Titans',    min: 100000, max: 10000000 },
  popular : { content: 'Popular',   min: 10000,  max: 100000 },
  plebs   : { content: 'Plebs',     min: 100,    max: 10000 },
  skills  : { content: 'Skills' },
}

const TWEETS_TABS = {
  today    : { content: "Today" },
  week     : { content: "Week" },
  month    : { content: "Month" },
  year     : { content: "Year" },
  all_time : { content: "All Time" },
}

export default function Leaderboards(props) {

  const { loadEndorsements } = useEndorsements();

  const [bitcoiners, setBitcoiners] = useImmer(null);
  const [tweets, setTweets] = useImmer(null);
  const [markets, setMarkets] = useImmer(null);
  const [category, setCategory] = useState('bitcoiners');
  const [endorsementFilter, setEndorsementFilter] = useState('developer');
  const [tweetsTab, setTweetsTab] = useState('today');
  const [bitcoinerTab, setBitcoinerTab] = useState('titans');
  const [showInfo, setShowInfo] = useState(false);
  const [selectedUser, setSelectedUser] = useImmer(null);
  const [showEndorsements, setShowEndorsements] = useState(false);
  const [showRate, setShowRate] = useState(false);

  const loadBitcoiners = async value => {
    const range = BITCOINER_TABS[value];
    const { data } = await axios.get(`/frontend/leaderboard/users/bitcoiner/following/bitcoiner/between/${range.min}/${range.max}`);

    setBitcoiners(Array.isArray(data) ? data : Object.values(data));
    // console.log('data:', data)
  }

  useEffect(() => { 
    const loadTweets = async () => {
      const { data } = await axios.get(`/frontend/leaderboard/tweets/bitcoiner/tweets/days/7000`);
      // console.log(data);

      setTweets(data);
    }

    const loadMarkets = async () => {
      const { data } = await axios.get("https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=25&page=1&sparkline=false");
      setMarkets(data);
    }

    loadBitcoiners(bitcoinerTab);
    // loadTweets();
    loadMarkets();
  }, []);

  useEffect(() => {
    const loadUsers = async () => {
      const { data } = await axios.get(`/frontend/endorsements/type/${endorsementFilter}`);
      setBitcoiners(data);
    }

    category === 'bitcoiners' && bitcoinerTab === 'skills' && loadUsers();
  }, [category, bitcoinerTab, endorsementFilter]);

  const onClickUser = async user => {
    setSelectedUser(user);
    setShowInfo(true);

    if (user && !user._endorsements) {
      const { endorsements, endorsements_auth } = await loadEndorsements(user.twitter_username);
      
      setSelectedUser(draft => {  
        if (endorsements) {
          draft._endorsements = endorsements;
          draft._endorsements_auth = endorsements_auth
        }
      });

      setBitcoiners(draft => {
        const index = draft?.findIndex(user => user.twitter_id === user.twitter_id) ?? -1;

        if (index !== -1) {
          draft[index]._endorsements = endorsements;
          draft[index]._endorsements_auth = endorsements_auth
        }
      });
    }
  }

  const updateEndorsement = type => {
    setSelectedUser(draft => {
      const prevEndorsed = draft._endorsements_auth[type] !== 0;

      draft._endorsements_auth[type] = prevEndorsed ? 0 : 1;
      draft._endorsements[type] += prevEndorsed ? -1 : 1;
    });

    setBitcoiners(draft => {
      const index = draft?.findIndex(user => user.twitter_id === selectedUser.twitter_id) ?? -1;

      if (index !== -1) {
        const prevEndorsed = draft[index]._endorsements_auth[type] !== 0;
        
        draft[index]._endorsements_auth[type] = prevEndorsed ? 0 : 1;
        draft[index]._endorsements[type] += prevEndorsed ? -1 : 1;
      }
    });
  }

  const selectTweets = () => {
    setCategory('tweets');
  }

  const selectBitcoiners = () => {
    setCategory('bitcoiners');
  }

  const selectMarketCap = () => {
    setCategory('market-cap');
  }

  const onUpdateTabBitcoiners = value => {
    setBitcoinerTab(value);
    value !== 'skills' && loadBitcoiners(value);
  }

  const onUpdateTabTweets = value => {
    setTweetsTab(value);
  }

  const renderTabs = () => {
    if (category === 'market-cap') {
      return;
    }

    const tabs = category === 'bitcoiners' ? BITCOINER_TABS : TWEETS_TABS;
    const selected = category === 'bitcoiners' ? bitcoinerTab : tweetsTab;
    
    const onChangeTab = category === 'bitcoiners' ? onUpdateTabBitcoiners : onUpdateTabTweets;

    return (
      <div className="tabs">
        { Object.entries(tabs).map(([id, item]) => (
          <div key={id} className={classNames("item", item.className, { selected: id === selected })} onClick={() => onChangeTab(id)} role='button'>{ item.content }</div>
        ))}
      </div>
    );
  }

  const renderMenuItems = () => {
    const items = Object.entries(ENDORSEMENT_TYPES);
    const renderItems = [];

    for (let i = 0; i < items.length; ++i) {
      const [id, type] = items[i];
      const [_, nextType] = items[i+1] || [];

      renderItems.push(<DropdownMenu.Item key={id} onClick={() => setEndorsementFilter(id)}>{ type.phrase.one }</DropdownMenu.Item>);

      if (nextType && nextType.color !== type.color) {
        renderItems.push(<DropdownMenu.Separator />);
      }
    }

    return renderItems;
  }

  const renderBitcoinersSubfilter = () => {
    return (
      <div className="endorsement">
        <div>Skill Endorsement</div>
        <DropdownMenu.Root>
          <DropdownMenu.Trigger className={classNames("filter-dropdown", ENDORSEMENT_TYPES[endorsementFilter]?.color)}>
            <div>{ ENDORSEMENT_TYPES[endorsementFilter]?.phrase.one || 'Show All' }</div>
            <ArrowDownIcon />
          </DropdownMenu.Trigger>

          <DropdownMenu.Portal>
            <DropdownMenu.Content align="end" className="__dropdown-menu __dropdown-menu-vertical-gap" >
              { renderMenuItems() }
            </DropdownMenu.Content>
          </DropdownMenu.Portal>
        </DropdownMenu.Root>
      </div>
    )
  }

  return (
    <div className="__leaderboards">
      {/* <BitcoinersNetworkLogo /> */}

      <div className="sticky-navigation">
        <SearchBar onClickUser={onClickUser}   />
        <div className="categories">
          <SquareButton icon={<ChatIcon />} selected={category === 'tweets'} onClick={selectTweets}>Tweets</SquareButton>
          <SquareButton icon={<UserIcon />} selected={category === 'bitcoiners'} onClick={selectBitcoiners}>Bitcoiners</SquareButton>
          <SquareButton icon={<AstrologyIcon />} selected={category === 'market-cap'} onClick={selectMarketCap}>Market Cap</SquareButton>
        </div>

        { renderTabs() }
        <div className={classNames("subfilter", { empty: category === 'bitcoiners' && bitcoinerTab !== 'skills' })}>
          { category === 'bitcoiners' && bitcoinerTab === 'skills' && renderBitcoinersSubfilter() }
          { category === 'market-cap' && <span>Displaying <strong>Top 25 Coins</strong> By Market Cap</span> }
        </div>
      </div>

      <div className="content">
        { category === 'bitcoiners' && <BitcoinersRanking users={bitcoiners} onClickUser={onClickUser} /> }
        { category === 'market-cap' && <Markets markets={markets} />}
      </div>

      <UserInfoPanel user={selectedUser} onClickBadge={() => setShowRate(true)} show={showInfo} onHide={() => setShowInfo(false)} onClickEndorse={() => setShowEndorsements(true)} />
      <CommunityRateModal show={showRate} onHide={() => setShowRate(false)} user={selectedUser} />
      <EndorsementModal show={showEndorsements} onHide={() => setShowEndorsements(false)} user={selectedUser} onToggleEndorsement={updateEndorsement} />
    </div>
  );
}
