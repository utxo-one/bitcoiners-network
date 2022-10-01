import * as Dialog from "@radix-ui/react-dialog";
import UserTypeBadge from '../../components/UserTypeBadge/UserTypeBadge';
import Button from '../../layout/Button/Button';

const USER_TYPES = ['bitcoiner', 'shitcoiner', 'nocoiner'];

import './CommunityRateModal.scss';

export default function CommunityRateModal({ show, onHide }) {
  return (
    <Dialog.Root open={show} onOpenChange={onHide}>
      <Dialog.Portal>
        <Dialog.Overlay className="__dialog-overlay">
          <Dialog.Content className="__community-rate-modal __modal __modal-center">
            <Dialog.Close asChild><div role="button" className='__modal-close-icon'>×</div></Dialog.Close>
            <Dialog.Title className="title">Rate This User</Dialog.Title>

              <p className="description">Help us correctly identify this user by selecting which category that he belongs to:</p>
              
              <div className="user-types">
                { USER_TYPES.map(type => (
                  <UserTypeBadge key={type} userType={type} />
                ))}
              </div>
                
              <div className="close-button">
                <Dialog.Close asChild><Button variant='clear'>Close</Button></Dialog.Close>
              </div>
          </Dialog.Content>
        </Dialog.Overlay>
      </Dialog.Portal>
    </Dialog.Root>
  )  
}
