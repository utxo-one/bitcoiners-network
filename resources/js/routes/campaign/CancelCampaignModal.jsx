import { useEffect, useState } from "react";
import axios from "axios";
import * as Dialog from "@radix-ui/react-dialog";
import { CompactNumberFormat } from "../../utils/NumberFormatting";

import Box from "../../layout/Box/Box";

import './CancelCampaignModal.scss';
import Button from "../../layout/Button/Button";
import { useNavigate } from "react-router-dom";

export default function CancelCampaignModal({ show, onHide, availableSats }) {

  // const [cancelSuccess, setCancelSuccess] = useState(true);
  const [cancelSuccess, setCancelSuccess] = useState(false);

  const [cancellingCampaign, setCancellingCampaign] = useState(false);

  console.log('availableSats:', availableSats)

  useEffect(() => {
    if (show) {
      setCancelSuccess(false);
      setCancellingCampaign(false);
    }
  }, [show]);

  const navigate = useNavigate();

  const onCancelCampaign = async () => {
    setCancellingCampaign(true);
    
    await axios.delete('/frontend/follow/mass-follow');

    setCancelSuccess(true);
  }

  const renderConfirm = () => (
    <>
      <p>After stopping this campaign, you will have <strong>{ CompactNumberFormat(availableSats, { digits: 12 }) }</strong> Sats available as credits for future campaigns.</p>

      <Box variant='info'>
        We only charge Sats for Twitter accounts that were succesfully followed.
      </Box>

      <div className="footer">
        <Dialog.Close asChild><Button disabled={cancellingCampaign} className="cancel" variant='clear'>Cancel</Button></Dialog.Close>
        <Button disabled={cancellingCampaign} loading={cancellingCampaign} className="submit" onClick={onCancelCampaign}>Confirm</Button>
      </div>
    </>
  );

  const renderCancelled = () => (
    <>
      <Box variant='info'>
      Your campaign has been cancelled. Any Sats left in your balance can be used for future campaigns.
      </Box>

      <Button className="back-to-dash" onClick={() => navigate('/')}>Back to Dashboard</Button>
    </>
  );

  const canClose = !cancellingCampaign && !cancelSuccess;

  return (
    <Dialog.Root open={show} onOpenChange={canClose ? onHide : null}>
        <Dialog.Portal>
          <Dialog.Overlay className="__dialog-overlay" >
            <Dialog.Content className="__cancel-campaign-modal __modal __modal-center">
              { canClose && <Dialog.Close asChild><div role="button" className='__modal-close-icon'>×</div></Dialog.Close> }
              <Dialog.Title className="title">Cancel Campaign</Dialog.Title>
                { cancelSuccess ? renderCancelled() : renderConfirm() }
            </Dialog.Content>
          </Dialog.Overlay>
        </Dialog.Portal>
    </Dialog.Root>
  )
}
