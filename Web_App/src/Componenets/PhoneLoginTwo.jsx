import photo from "../images/Login.jpg";
import "react-phone-number-input/style.css";
import PhoneInput from "react-phone-number-input";
import React, { useState } from "react";
import validator from "validator";
import Modal from "react-bootstrap/Modal";
import OTPmodal2 from "./OTPmodal2";
import { translate } from "../utils";
import { useSelector } from "react-redux";
import { webSettingsData } from "../store/reducers/websettingsReducer";

function PhoneLoginTwo(props) {
    const [PhoneOTPModalShow, setPhoneOTPModalShow] = React.useState(false);
    const [phonenum, setPhonenum] = useState(null);

    // const navigate = useNavigate();

    const [value, setValue] = useState();
    const [error, setError] = useState(
        "",
        setTimeout(() => {
            if (error !== "") setError("");
        }, 5000)
    );

    const websettings = useSelector(webSettingsData);


    const handleGetOtp = () => {
        if (value === undefined) {
            setError("Please enter phone number!");
        } else if (validator.isMobilePhone(value)) {
            setPhonenum(value);
            setPhoneOTPModalShow(true);
        } else {
            setError("Enter a valid phone number");
        }
    };


    return (
        <>
            <div>
                <Modal {...props} size="xl" aria-labelledby="contained-modal-title-vcenter" centered dialogClassName="border-radius-2">
                    <div className="ModalWrapper44" id="ModalWrapper44">
                        <div style={{ width: "100%", height: "100%", objectFit: "cover", borderRadius: "20px" }} id="login_img4">
                            <img className="ModalImg4" src={photo} alt="" />
                            <div className="logo-img-overlay">
                                <img src={websettings && websettings.web_header_logo} alt="" id="logo4" />
                            </div>
                            <div className="logo-text4">
                                <h4>{translate("beautifulltheme")}</h4>
                                <p> {translate("bestinvestment")}</p>
                            </div>
                        </div>

                        <div id="modal-content">
                            <Modal.Header closeButton>
                                <Modal.Title id="contained-modal-title-vcenter">{translate("loginTxt")}</Modal.Title>
                            </Modal.Header>
                            <Modal.Body>
                                <div className="AC">
                                    <div className="welcom-back4">
                                        <h5>
                                            <strong> {translate("enter-your-mobile-number")} </strong>
                                        </h5>
                                        <div id="Welcom" style={{ fontSize: "14px" }}>
                                            {" "}
                                            {translate("six-didgit-code")}
                                        </div>
                                    </div>
                                    <form className="my-2">
                                        <div className="mb-3">
                                            <PhoneInput  className="phoneInput" placeholder="Enter your phone number" defaultCountry={process.env.REACT_APP_DEFAULT_COUNTRY} international value={value} onChange={setValue} />
                                        </div>

                                        <div className="py-3">
                                            <p className="error-msg">{error}</p>
                                            <button
                                                type="button"
                                                className="btn   btn-lg  w-100"
                                                id="submitbutton"
                                                onClick={() => {
                                                    // props.onHide()
                                                    handleGetOtp();
                                                }}
                                            >
                                                {translate("reqOtpLbl")}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </Modal.Body>
                        </div>
                    </div>
                </Modal>
            </div>

            {phonenum !== null ? (
                <OTPmodal2 setisloginloading={props.setisloginloading} setIsLogout={props.setIsLogout} phonenum={phonenum} onPhonenumHide={props.onHide()} show={PhoneOTPModalShow} onHide={() => setPhoneOTPModalShow(false)} />
            ) : null}
        </>
    );
}

export default PhoneLoginTwo;
