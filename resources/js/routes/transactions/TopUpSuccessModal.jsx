import * as Dialog from "@radix-ui/react-dialog";
import { CompactNumberFormat } from "../../utils/NumberFormatting";

import Box from "../../layout/Box/Box";
import Button from "../../layout/Button/Button";

import PurpleBoltIcon from "../../assets/icons/PurpleBoltIcon";

import './TopUpSuccessModal.scss';
import { useContext } from "react";
import AppContext from "../../store/AppContext";

export default function TopUpSuccessModal({ show, onHide }) {

  const [state] = useContext(AppContext);
  
  const { availableSats } = state;

  return (
    <Dialog.Root open={show} onOpenChange={onHide}>
      <Dialog.Portal>
        <Dialog.Overlay className="__dialog-overlay">
          <Dialog.Content className="__campaign-success-modal __modal __modal-center">
            <Dialog.Close asChild><div role="button" className='__modal-close-icon'>×</div></Dialog.Close>

            <PurpleBoltIcon className="bolt-icon" />
            <h2>Top Up Success</h2>

            <Box variant='info'>
              You can now start Follow Bitcoiners Campaigns and manually follow or unfollow users.
            </Box>

            <div className="item">
              <div className="label">Balance</div>
              <div className="value"><span className="number">{ CompactNumberFormat(availableSats, { digits: 12 }) }</span> Sats</div>
            </div>

            <Button variant="clear" onClick={onHide}>Close</Button>
          </Dialog.Content>
        </Dialog.Overlay>
      </Dialog.Portal>
    </Dialog.Root>
  );
}
