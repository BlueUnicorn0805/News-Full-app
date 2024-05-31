import React, { useState, useEffect } from "react";
import Button from "react-bootstrap/Button";
import Modal from "react-bootstrap/Modal";
import ReactPlayer from "react-player";
import { AiOutlineClose } from "react-icons/ai";

function VideoPlayerModal(props) {
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const timer = setTimeout(() => {
      setIsLoading(false);
    }, 2000);

    return () => clearTimeout(timer);
  }, [props.show]);

  useEffect(() => {
    setIsLoading(true);
  }, [props.url]);

  return (
    <Modal {...props} size="xl" aria-labelledby="contained-modal-title-vcenter" centered>
      <Modal.Body id="vps-modal-body">
        {isLoading ? (
          <div className="loader-container">
            <span className="loader"></span>
          </div>
        ) : (
          <>
            <Button id="vps-modal-btnclose" onClick={props.onHide}>
              <AiOutlineClose id="btnClose-logo" size={20} />
            </Button>
            {props.type_url === "url_other" ? (
              <iframe title="Video" src={props.url} className="other_url" allowFullScreen></iframe>
            ) : (
              <ReactPlayer width="100%" height="40rem" url={props.url} controls={true} />
            )}
          </>
        )}
      </Modal.Body>
    </Modal>
  );
}

export default VideoPlayerModal;
