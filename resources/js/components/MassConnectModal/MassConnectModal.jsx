import { useEffect, useState } from "react";
import axios from "axios";
import classNames from "classnames";
import * as Dialog from "@radix-ui/react-dialog";
import { CompactNumberFormat } from "../../utils/NumberFormatting";

import Box from "../../layout/Box/Box";
import AmountSlider from "./AmountSlider";
import ButtonWithLightning from "../../layout/Button/ButtonWithLightning";

import './MassConnectModal.scss';
import CampaignSuccessModal from "../CampaignSuccessModal/CampaignSuccessModal";

const DEFAULT_AMOUNT = 50;
const SLIDER_MAX = 100;
const MAX_FOLLOWS = 5000;

export default function MassConnectModal({ show, onHide, onCampaignStart }) {

  const [sliderValue, setSliderValue] = useState([DEFAULT_AMOUNT]);
  const [rate, setRate] = useState(null);
  const [proceessingCampaign, setProcessingCampaign] = useState(false);
  const [availableSats, setAvailableSats] = useState(0);
  const [showCampaignSuccess, setShowCampaignSuccess] = useState(false);
  
  // TODO -> get from endpoint
  const [totalAvailable, setTotalAvailable] = useState(MAX_FOLLOWS);
  const [totalUsers, setTotalUsers] = useState(() => totalAvailable * DEFAULT_AMOUNT / 100);

  const balanceVisible = availableSats > 0;
  const campaignCost = totalUsers * rate?.pricing.follow;
  const missingSats = campaignCost - availableSats;

  let ctaTitle = 'Pay Via Lightning';
  
  if (balanceVisible) {
    ctaTitle = (missingSats > 0) ? 'Top up Via Lightning' : 'Start Campaign';
  }

  useEffect(() => {
    const getRate = async () => {
      const { data } = await axios.get('/frontend/rates');
      setRate(data);
    }

    const getBalance = async () => {
      const { data } = await axios.get('/frontend/user/available-balance');
      setAvailableSats(data || 0);

      console.log('data:', data)
    }

    getRate();
    getBalance();
  }, []);

  const changeTotalUsers = e => {
    let total = Math.max(0, Math.min(MAX_FOLLOWS, parseInt(e.target.value, 10)));
    if (Number.isNaN(total)) {
      total = 0;
    }
    
    setTotalUsers(total);
    setSliderValue([Math.round(total * 100 / totalAvailable)]);
  }

  const changeSliderValue = values => {
    setSliderValue(values);
    setTotalUsers(Math.round(totalAvailable * values[0] / 100));
  }

  const topupLightning = async () => {
    const { data } = await axios.post('/frontend/transaction/deposit', {
      amount: totalUsers * rate?.pricing.follow,
    });

    console.log('creating invoice for amount:', totalUsers * rate?.pricing.follow);
    window.location.href = data.checkoutLink;
  }

  const startCampaign = async () => {
    // const { data } = await axios.post('/frontend/follow/mass-follow', {
    //   amount: totalUsers,
    // });

    onHide();
    setShowCampaignSuccess(true);
  }

  const handleCta = async () => {

    setProcessingCampaign(true);
    
    if (missingSats > 0) {
      topupLightning();
    }

    else {
      startCampaign();
    }

    setProcessingCampaign(false);
  }

  return (
    <>
      <Dialog.Root open={show} onOpenChange={onHide}>
        <Dialog.Portal>
          <Dialog.Overlay className="__dialog-overlay">
            <Dialog.Content className="__mass-connect-modal __modal __modal-center">
              <Dialog.Close asChild><div role="button" className='__modal-close-icon'>×</div></Dialog.Close>
              <Dialog.Title className="title">Mass Follow</Dialog.Title>
                <AmountSlider value={sliderValue} onValueChange={changeSliderValue} min={1} max={SLIDER_MAX} />

                <div className="item">
                  <div className="label user">Users</div>
                  <input type="number" value={totalUsers} onChange={changeTotalUsers} />
                </div>

                <div className={classNames("item", {'balance-hidden': !balanceVisible})}>
                  <div className="label">Estimated Time</div>
                  <div className="value"><span className="number">{ Math.ceil(totalUsers / rate?.limits.dailyFollows) }</span> days</div>
                </div>

                { balanceVisible && (
                  <Box variant='info' className={classNames('top-up-required', { invisible: missingSats < 0 })}>
                    To start this campaign, you must top up <strong>{ CompactNumberFormat(Math.max(missingSats, 0), { digits: 12 }) }</strong> Sats.
                  </Box>
                )}

                <div className="item">
                  <div className="label">Total Cost</div>
                  <div className="value"><span className="number">{ CompactNumberFormat(campaignCost, { digits: 12 }) }</span> Sats</div>
                </div>

                { balanceVisible && (
                  <div className="item">
                    <div className="label">Your Balance</div>
                    <div className="value"><span className="number">{ CompactNumberFormat(availableSats, { digits: 12 }) }</span> Sats</div>
                  </div>
                )}

                <ButtonWithLightning disabled={proceessingCampaign || !totalUsers} loading={proceessingCampaign} onClick={handleCta} className="pay-via-ln">{ ctaTitle }</ButtonWithLightning>
            </Dialog.Content>
          </Dialog.Overlay>
        </Dialog.Portal>
      </Dialog.Root>

      <CampaignSuccessModal show={showCampaignSuccess} redirectToCampaign cost={campaignCost} accounts={totalUsers} />
    </>
  )
}
