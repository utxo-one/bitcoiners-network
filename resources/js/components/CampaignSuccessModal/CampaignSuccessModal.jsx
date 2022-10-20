import * as Dialog from "@radix-ui/react-dialog";
import { useNavigate } from "react-router-dom";
import { CompactNumberFormat } from "../../utils/NumberFormatting";

import Box from "../../layout/Box/Box";
import Button from "../../layout/Button/Button";

import PurpleBoltIcon from "../../assets/icons/PurpleBoltIcon";
import './CampaignSuccessModal.scss';

export default function CampaignSuccessModal({ show, onHide, redirectToCampaign, cost, accounts }) {

  const navigate = useNavigate();

  return (
    <Dialog.Root open={show} onOpenChange={redirectToCampaign ? onHide : null}>
      <Dialog.Portal>
        <Dialog.Overlay className="__dialog-overlay">
          <Dialog.Content className="__campaign-success-modal __modal __modal-center">
            { !redirectToCampaign && <Dialog.Close asChild><div role="button" className='__modal-close-icon'>×</div></Dialog.Close> }

            <PurpleBoltIcon className="bolt-icon" />
            <h2>Campaign Started</h2>

            <Box variant='info'>
              Your Mass follow Campaign will start immediately. Get ready to unleash the power of Bitcoin Twitter.
            </Box>

            <div className="item">
              <div className="label">Total Cost</div>
              <div className="value"><span className="number">{ CompactNumberFormat(cost, { digits: 12 }) }</span> Sats</div>
            </div>

            <div className="item">
              <div className="label">Users</div>
              <div className="value"><span className="number">{ CompactNumberFormat(accounts, { digits: 12 }) }</span> Users</div>
            </div>

            { redirectToCampaign
            ? <Button onClick={() => navigate('/campaign')}>View Campaign</Button>
            : <Button onClick={onHide}>Close</Button>
            }
          </Dialog.Content>
        </Dialog.Overlay>
      </Dialog.Portal>
    </Dialog.Root>
  );
}
