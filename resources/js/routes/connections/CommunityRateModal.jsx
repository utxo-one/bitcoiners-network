import * as Dialog from "@radix-ui/react-dialog";
import axios from "axios";
import classNames from "classnames";
import { useEffect, useState } from "react";
import UserTypeBadge from '../../components/UserTypeBadge/UserTypeBadge';
import Button from '../../layout/Button/Button';
import RadialBar from "../../layout/Connections/RadialBar";
import { calculatePercentages, CompactNumberFormat } from "../../utils/NumberFormatting";

const USER_TYPES = ['bitcoiner', 'shitcoiner', 'nocoiner'];

import './CommunityRateModal.scss';

export default function CommunityRateModal({ show, user, onHide }) {

  const [votes, setVotes] = useState(null);
  const [selectedType, setSelectedType] = useState(null);

  useEffect(() => {
    if (show) {
      setVotes(null);
      setSelectedType(null);
    }
  }, [show]);

  const onCastVote = async type => {
    setSelectedType(type);
    const { data } = await axios.post(`/frontend/classify/${user.twitter_username}/${type}`);
    setVotes(data);
  }

  const renderVotes = () => {
    const userTypes = ['bitcoiner', 'shitcoiner', 'nocoiner'];
  
    const percentages = user && calculatePercentages(votes, userTypes);
  
    return (
      <div className="votes-chart">
        { userTypes.map(type => (
          <div key={type} className={classNames("type", type)}>
            <div className="chart">
              <RadialBar className={type} percent={votes.total === 0 ? 0 : votes[type] / votes.total * 100} />
              <div className="chart-percent">
                { percentages?.[type] || 0 }%
              </div>
            </div>
            <div className="count">{ CompactNumberFormat(votes[type]) } votes</div>
            {/* <div className="user-type">{ USER_TYPES[type].phrase }</div> */}
          </div>
        ))}
      </div>
    );
  }

  return (
    <Dialog.Root open={show} onOpenChange={onHide}>
      <Dialog.Portal>
        <Dialog.Overlay className="__dialog-overlay">
          <Dialog.Content className="__community-rate-modal __modal __modal-center">
            <Dialog.Close asChild><div role="button" className='__modal-close-icon'>×</div></Dialog.Close>
            <Dialog.Title className="title">Help us classify this user</Dialog.Title>
              <div className="user-types">
                { USER_TYPES.map(type => (
                  <UserTypeBadge key={type} userType={type} role="button" onClick={() => onCastVote(type)} variant={selectedType === type ? 'solid' : 'outline'}  />
                ))}
              </div>
              
              { votes && renderVotes() }
                
              <div className="close-button">
                <Dialog.Close asChild><Button variant='clear'>Close</Button></Dialog.Close>
              </div>
          </Dialog.Content>
        </Dialog.Overlay>
      </Dialog.Portal>
    </Dialog.Root>
  )  
}
