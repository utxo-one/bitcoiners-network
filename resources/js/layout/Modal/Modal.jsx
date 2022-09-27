import classNames from "classnames";
import { useRef } from "react";
import { Modal as ReactOverlaysModal } from "react-overlays";
import { CSSTransition } from "react-transition-group";

import './Modal.scss';

export default function Modal({ show, onHide, children, className }) {

  const backdropRef = useRef();
  const modalRef = useRef();

  const renderBackdrop = (props) => (
    <CSSTransition in={show} appear nodeRef={backdropRef} classNames='__modal-backdrop' timeout={500}>
      <div className="__modal-backdrop" {...props} ref={backdropRef} />
    </CSSTransition>
  );

  return (
    <ReactOverlaysModal show={show} renderBackdrop={renderBackdrop} onHide={onHide}>
      <CSSTransition in={show} appear nodeRef={modalRef} className={classNames('__modal-dialog', className)} classNames='__modal-dialog' timeout={500}>
        <div ref={modalRef}>{ children }</div>
      </CSSTransition>
    </ReactOverlaysModal>
  );
}
