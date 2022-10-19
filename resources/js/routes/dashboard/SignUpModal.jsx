import * as Dialog from "@radix-ui/react-dialog";
import BitcoinersNetworkLogo from "../../assets/icons/BitcoinersNetworkLogo";
import Button from "../../layout/Button/Button";
import TwitterButton from "../../layout/Button/TwitterButton";

import './SignUpModal.scss';

export default function SignUpModal({ show, onHide }) {
  return (
    <Dialog.Root open={show} onOpenChange={onHide}>
      <Dialog.Portal>
        <Dialog.Overlay className="__dialog-overlay">
          <Dialog.Content className="__sign-up-modal __modal __modal-center">
            <Dialog.Close asChild><div role="button" className='__modal-close-icon'>×</div></Dialog.Close>

            <BitcoinersNetworkLogo className='logo' />
            <h2>Unleash the Power of Bitcoin Twitter</h2>

            <div className="sub">Sign up and discover thousands of Bitcoiners in our Twitter network.</div>

            {/* <div className="w"> */}
            <TwitterButton as="a" href='/get_started'>Sign Up Via Twitter</TwitterButton>

            <div className="learn-more">
              <Button variant="clear" onClick={onHide} as="a" href="/">Learn More</Button>
            </div>
          </Dialog.Content>
        </Dialog.Overlay>
      </Dialog.Portal>
    </Dialog.Root>
  );
}
