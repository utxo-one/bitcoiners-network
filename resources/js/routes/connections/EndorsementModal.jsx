
import { useEffect, useContext } from "react";
import { useImmer } from "use-immer";
import axios from "axios";
import classNames from "classnames";
import AppContext from "../../store/AppContext";
import { CompactNumberFormat } from "../../utils/NumberFormatting";
import { ENDORSEMENT_TYPES } from "../../utils/Types";

import Button from '../../layout/Button/Button';
import * as Dialog from "@radix-ui/react-dialog";
import * as DropdownMenu from "@radix-ui/react-dropdown-menu";

import CheckIcon from "../../assets/icons/CheckIcon";
import './EndorsementModal.scss';

export default function EndorsementModal({ show, user, onHide, onToggleEndorsement }) {

  const [visibleItems, setVisibleItems] = useImmer([]);
  
  const [state] = useContext(AppContext);

  const { _endorsements, _endorsements_auth } = user || {};
  const { currentUser } = state;

  const viewingOwnProfile = currentUser && currentUser?.twitter_id === user?.twitter_id;

  useEffect(() => {
    if (show) {
      setVisibleItems(Object.keys(ENDORSEMENT_TYPES).filter(id => user._endorsements?.[id]));
    }
  }, [show]);

  const addEndorsement = async (type, addToList) => {
    // optimistic update
    axios.post('/frontend/endorse/', { twitterId: user.twitter_id, type });

    addToList && setVisibleItems(draft => {
      draft.push(type);
    });

    onToggleEndorsement(type);
  }

  const removeEndorsement = async type => {
    // optimistic update
    axios.delete('/frontend/endorse/', { data: { twitterId: user.twitter_id, type }});

    onToggleEndorsement(type);
  }

  const renderMenuItems = () => {
    const items = Object.entries(ENDORSEMENT_TYPES).filter(([id]) => !visibleItems.includes(id));
    const renderItems = [];

    for (let i = 0; i < items.length; ++i) {
      const [id, type] = items[i];
      const [_, nextType] = items[i+1] || [];

      renderItems.push(<DropdownMenu.Item key={id} onClick={() => addEndorsement(id, true)}>{ type.phrase.one }</DropdownMenu.Item>);

      if (nextType && nextType.color !== type.color) {
        renderItems.push(<DropdownMenu.Separator />);
      }
    }

    return renderItems;
  }
  
  return (
    <Dialog.Root open={show} onOpenChange={onHide}>
      <Dialog.Portal>
        <Dialog.Overlay className="__dialog-overlay">
          <Dialog.Content className="__endorsement-modal __modal __modal-center">
            <Dialog.Close asChild><div role="button" className='__modal-close-icon'>×</div></Dialog.Close>
            <Dialog.Title className="title">Endorsements</Dialog.Title>
              <div className="endorsements-list">
                { Object.entries(ENDORSEMENT_TYPES).filter(([id]) => visibleItems.includes(id)).map(([id, type]) => (
                  <div key={id} className='endorsement-row'>
                    <div key={id} className={classNames('badge', `badge-${type.color}`)}>
                      { type.phrase.one }
                      { _endorsements && _endorsements[id] > 0 && (
                        <>
                          <span className="separator">&middot;</span>
                          <span className="votes-count">{ CompactNumberFormat(_endorsements[id], { digits: 3 }) }</span>
                        </>
                      )}
                    </div>

                    { !viewingOwnProfile && (
                      _endorsements_auth?.[id] > 0
                      ? <CheckIcon className="checked" role='button' onClick={() => removeEndorsement(id)} />
                      : <button className="add" onClick={() => addEndorsement(id)} ><span>+</span></button>
                      )
                    }
                  </div>
                ))}
              </div>
              
              { visibleItems.length !== Object.keys(ENDORSEMENT_TYPES).length && !viewingOwnProfile && (
                <DropdownMenu.Root>
                  <div className="add-new-endorsement">
                    <DropdownMenu.Trigger className="button">+ Add Endorsement</DropdownMenu.Trigger>
                  </div>

                  <DropdownMenu.Portal>
                    <DropdownMenu.Content avoidCollisions={false} className="__dropdown-menu __connections-filter" >
                      { renderMenuItems() }
                    </DropdownMenu.Content>
                  </DropdownMenu.Portal>
                </DropdownMenu.Root>
              )}

              <div className="close-button">
                <Dialog.Close asChild><Button variant='clear'>Close</Button></Dialog.Close>
              </div>
          </Dialog.Content>
        </Dialog.Overlay>
      </Dialog.Portal>
    </Dialog.Root>
  )  
}
