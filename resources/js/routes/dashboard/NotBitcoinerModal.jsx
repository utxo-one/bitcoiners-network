import * as Dialog from "@radix-ui/react-dialog";
import { useState } from "react";

import Box from "../../layout/Box/Box";
import Button from "../../layout/Button/Button";
import ButtonWithLightning from "../../layout/Button/ButtonWithLightning";

import './NotBitcoinerModal.scss';

const USER_TYPE_PHRASE = {
  shitcoiner : "Shitcoiner",
  nocoiner   : "Nocoiner",
}

export default function NotBitcoinerModal({ show, onHide, user }) {

  const [linkCopied, setLinkCopied] = useState();
  const [processingTopUp, setProcessingTopUp] = useState(false);

  const { protocol, host } = window.location;

  const profileUrl = `${host}/u/profile/${user?.twitter_username}`;

  const onClickCopy = () => {
    setLinkCopied(true);
    navigator.clipboard.writeText(`${protocol}//${profileUrl}`);
  }

  const onClickVerify = async () => {
    setProcessingTopUp(true);

    const { data } = await axios.post('/frontend/transaction/deposit', {
      amount      : 10,
      redirectUrl : `/u/transactions?top_up_time=${Date.now()}&verified_bitcoiner=true`
    });

    window.location.href = data.checkoutLink;
  }

  return (
    <Dialog.Root open={show} onOpenChange={onHide}>
      <Dialog.Portal>
        <Dialog.Overlay className="__dialog-overlay">
          <Dialog.Content className="__not-bitcoiner-modal __modal __modal-center">
            <Dialog.Close asChild><div role="button" className='__modal-close-icon'>×</div></Dialog.Close>

            <h3 className={user?.type}>{user?.type === 'shitcoiner' ? '💩 Shitcoiner Alert 💩' : '🤡 Nocoiner Alert 🤡' }</h3>

            <p>Oh no! We've labeled you a { USER_TYPE_PHRASE[user?.type] }. Did we get that wrong?</p>
            <p><strong>Share your Profile</strong> on Twitter and let others vote for you as a Bitcoiner:</p>

            <input type="text" className="url" value={profileUrl} readOnly tabIndex={-1}/>

            <Button variant='clear' className="copy-link" onClick={onClickCopy}>{ linkCopied ? 'Link Copied' : 'Copy Link' }</Button>

            <div className="or-hr">Or</div>
            <ButtonWithLightning loading={processingTopUp} onClick={onClickVerify}>Verify with 10 sats</ButtonWithLightning>
            <Button disabled={processingTopUp} variant='clear' className="continue" onClick={onHide}>Continue as a { USER_TYPE_PHRASE[user?.type] }</Button>

          </Dialog.Content>
        </Dialog.Overlay>
      </Dialog.Portal>
    </Dialog.Root>
  );
}
