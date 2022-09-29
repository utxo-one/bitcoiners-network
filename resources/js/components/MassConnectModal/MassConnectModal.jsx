import { useEffect, useState } from "react";
import axios from "axios";
import { CompactNumberFormat } from "../../utils/NumberFormatting";
import * as Dialog from "@radix-ui/react-dialog";

import AmountSlider from "./AmountSlider";
import ButtonWithLightning from "../../layout/Button/ButtonWithLightning";
import './MassConnectModal.scss';

const DEFAULT_AMOUNT = 50;
const SLIDER_MAX = 100;
const MAX_FOLLOWS = 5000;

export default function MassConnectModal({ show, onHide }) {

  const [sliderValue, setSliderValue] = useState([DEFAULT_AMOUNT]);
  const [rate, setRate] = useState(null);
  const [creatingInvoice, setCreatingInvoice] = useState(false);
  
  // TODO -> get from endpoint
  const [totalAvailable, setTotalAvailable] = useState(MAX_FOLLOWS);
  const [totalUsers, setTotalUsers] = useState(() => totalAvailable * DEFAULT_AMOUNT / 100);

  useEffect(() => {
    const getRate = async () => {
      const { data } = await axios.get('/frontend/rates');
      setRate(data);
    }

    getRate();
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
    setTotalUsers(Math.round(totalAvailable * sliderValue[0] / 100));
  }

  const topupLightning = () => {
    const createDeposit = async () => {
      setCreatingInvoice(true);
      const { data } = await axios.post('/frontend/transaction/deposit', {
        amount: totalUsers * rate?.pricing.follow,
      });

      window.location.href = data.checkoutLink;
    }

    console.log('creating invoice for amount:', totalUsers * rate?.pricing.follow);
    createDeposit();
  }

  return (
    <Dialog.Root open={show} onOpenChange={onHide}>
      <Dialog.Portal>
        <Dialog.Overlay className="__dialog-overlay">
          <Dialog.Content className="__mass-connect-modal __dialog-center-modal">
            <Dialog.Title className="title">Mass Follow</Dialog.Title>
              <AmountSlider value={sliderValue} onValueChange={changeSliderValue} max={SLIDER_MAX} />

              <div className="item">
                <div className="label user">Users</div>
                <input type="number" value={totalUsers} onChange={changeTotalUsers} />
              </div>

              <div className="item estimation">
                <div className="label">Estimated Time</div>
                <div className="value"><span className="number">{ Math.ceil(totalUsers / rate?.limits.dailyFollows) }</span> days</div>
              </div>

              <div className="item">
                <div className="label">Total Cost</div>
                <div className="value"><span className="number">{ CompactNumberFormat(totalUsers * rate?.pricing.follow, { digits: 12 }) }</span> Sats</div>
              </div>

              <ButtonWithLightning disabled={creatingInvoice || !totalUsers} onClick={topupLightning} className="pay-via-ln">{ creatingInvoice ? '[ Loading ]' : 'Top up Via Lightning' }</ButtonWithLightning>

            {/* <Dialog.Close asChild><div>CLOSE</div></Dialog.Close> */}
          </Dialog.Content>
        </Dialog.Overlay>
      </Dialog.Portal>
    </Dialog.Root>
  )
}
