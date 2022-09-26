import classNames from "classnames";
import { Modal as ReactOverlaysModal } from "react-overlays";

import './Modal.scss';

export default function Modal({ show, onHide, children, className }) {

  const renderBackdrop = (props) => <div className="__modal-backdrop" {...props} />;

  return (
    <ReactOverlaysModal show={show} renderBackdrop={renderBackdrop} onHide={onHide} className={classNames('__modal-dialog', className)}>
      <div>{ children }</div>
    </ReactOverlaysModal>
  );
}
