import { useEffect, useState } from "react";
import axios from "axios";
import classNames from "classnames";
import * as Dialog from "@radix-ui/react-dialog";
import { CompactNumberFormat } from "../../utils/NumberFormatting";

import Box from "../../layout/Box/Box";
import AmountSlider from "./AmountSlider";
import ButtonWithLightning from "../../layout/Button/ButtonWithLightning";

import './TopUpModal.scss';

const DEFAULT_SLIDER = 25;
const DEFAULT_SATS = 5000;
const SLIDER_MAX = 100;
const MAX_SATS = 500000;

const RANGE_TO_SATS = [
  { value: 86, add: 10000, cumulative: 360000 },
  { value: 74, add: 8000, cumulative: 264000 },
  { value: 51, add: 6000, cumulative: 126000 },
  { value: 30, add: 4000, cumulative: 42000 },
  { value: 18, add: 2000, cumulative: 18000 },
  { value: 0, add: 1000, cumulative: 0 },
];

const rangeToSats = value => {
  let sats = 0;

  RANGE_TO_SATS.forEach(range => {
    if (value > range.value) {
      sats += range.add * (value - range.value)
      value = range.value;
    }
  });

  return sats;
}

const satsToRange = sats => {
  let approxRange = 0;

  RANGE_TO_SATS.some(range => {
    if (sats > range.cumulative) {
      approxRange = range.value + Math.round(((sats - range.cumulative) / range.add));
      return true;
    }
  });

  return approxRange;
}

export default function TopUpModal({ show, onHide }) {

  const [sliderValue, setSliderValue] = useState([DEFAULT_SLIDER]);
  const [rate, setRate] = useState(null);
  const [proceessingTopUp, setProcessingTopUp] = useState(false);
  
  // TODO -> get from endpoint
  const [totalSats, setTotalSats] = useState(DEFAULT_SATS);

  const costPerConnection = 100;

  useEffect(() => {
    const getRate = async () => {
      const { data } = await axios.get('/frontend/rates');
      setRate(data);
    }

    getRate();
  }, []);

  const changeTotalSats = e => {
    let total = Math.max(0, Math.min(MAX_SATS, parseInt(e.target.value, 10)));
    if (Number.isNaN(total)) {
      total = 0;
    }
    
    setSliderValue([satsToRange(total)]);
    setTotalSats(total);
  }

  const changeSliderValue = values => {
    setSliderValue(values);
    setTotalSats(rangeToSats(values[0]))
  }

  const onClickTopUp = async () => {
    
    setProcessingTopUp(true);
    const { data } = await axios.post('/frontend/transaction/deposit', {
      amount: totalSats
    });

    window.location.href = data.checkoutLink;
  }

  return (
    <Dialog.Root open={show} onOpenChange={proceessingTopUp ? null : onHide}>
      <Dialog.Portal>
        <Dialog.Overlay className="__dialog-overlay">
          <Dialog.Content className="__top-up-modal __modal __modal-center">
            <Dialog.Close asChild><div role="button" className='__modal-close-icon'>×</div></Dialog.Close>
            <Dialog.Title className="title">Top Up Required</Dialog.Title>
              <AmountSlider value={sliderValue} onValueChange={changeSliderValue} min={1} max={SLIDER_MAX} />

              <div className="item">
                <div className="label user">Sats</div>
                <input type="number" value={totalSats} onChange={changeTotalSats} />
              </div>

              <Box variant='info' className='top-up-required'>
              <strong>{ CompactNumberFormat(totalSats, { digits: 5 }) }</strong> Sats allows you to follow or unfollow <strong>{ CompactNumberFormat(Math.floor(totalSats / costPerConnection)) }</strong> users manually, or through a Mass Follow campaign.
              </Box>

              <ButtonWithLightning disabled={!totalSats} loading={proceessingTopUp} onClick={onClickTopUp} className="pay-via-ln">Top Up Via Lightning</ButtonWithLightning>
          </Dialog.Content>
        </Dialog.Overlay>
      </Dialog.Portal>
    </Dialog.Root>
  )
}
