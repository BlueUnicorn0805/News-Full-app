import Modal from "react-bootstrap/Modal";
import photo from "../images/Login.jpg";
import React, { useEffect, useState } from "react";
//otp
import OTPInput from "otp-input-react";
import {translate} from "../utils"

//firebase
import { authentication } from "../Firebase";
// import { RecaptchaVerifier, signInWithPhoneNumber } from "firebase/auth";
import { RecaptchaVerifier, signInWithPhoneNumber } from "firebase/auth";
import { loadMobileType, register } from "../store/reducers/userReducer";
import { useSelector } from "react-redux";
import { webSettingsData } from "../store/reducers/websettingsReducer";
import { toast } from "react-toastify";
import { useNavigate } from "react-router-dom";

function OTPmodal2(props) {
    const [OTP, setOTP] = useState(""); // eslint-disable-next-line
    const [confirmResult, setConfirmResult] = useState("");
    const [error, setError] = useState(
        "",
        setTimeout(() => {
            if (error !== "") setError("");
        }, 5000)
    );

    const navigate = useNavigate();

    const websettings = useSelector(webSettingsData);


    const resendOTP = () => {
        if (props.phonenum !== null) generateOTP(props.phonenum);
    };
    const generateRecaptcha = () => {
        if (!window.recaptchaVerifier) {
            window.recaptchaVerifier = new RecaptchaVerifier(
                "recaptcha-container",
                {
                    size: "invisible",
                    callback: (response) => {
                        // reCAPTCHA solved, allow signInWithPhoneNumber.
                    },
                },
                authentication
            );
        }
    };

    const generateOTP = (phonenum) => {
        //OTP Generation
        generateRecaptcha();
        let appVerifier = window.recaptchaVerifier;
        signInWithPhoneNumber(authentication, phonenum, appVerifier)
            .then((confirmationResult) => {
                window.confirmationResult = confirmationResult;
                setConfirmResult(confirmationResult);
                loadMobileType(true)
            })
            .catch((error) => {
                let errorMessage = '';
                    switch (error.code) {
                        case 'auth/too-many-requests':
                        errorMessage = 'Too many requests. Please try again later.';
                        break;
                        case 'auth/invalid-phone-number':
                        errorMessage = 'Invalid phone number. Please enter a valid phone number.';
                        break;
                        default:
                        errorMessage = 'An error occurred. Please try again.';
                        break;
                }
                 // display error message in a toast or alert
                 toast.error(errorMessage);
            });
    };

    useEffect(() => {
        if (props.phonenum !== null) {
            generateOTP(props.phonenum);
        }
        // eslint-disable-next-line
    }, [props.phonenum]);

    const submitOTP = async (e) => {
        e.preventDefault();

        let confirmationResult = window.confirmationResult;

        try {
            const response = await confirmationResult.confirm(OTP);

            // User verified successfully.
            props.setIsLogout(true);
            props.onHide();

            register(
                response.user.uid,
                "",
                "",
                response.user.phoneNumber,
                "mobile",
                "",
                "1",
                "",
                (response) => {
                    if (response.data.is_login === "1") {
                        // If new User then show the Update Profile Screen
                        navigate("/profile-update");
                    }
                    props.setisloginloading(false);
                },
                (error) => {
                    toast.error(translate("deactiveMsg"))
                }
            );
        } catch (error) {
            console.log(error);
            // User couldn't sign in (bad verification code?)
            setError("Invalid Code");
        }
    };

   
    return (
        <>
            <div>
                <Modal {...props} size="xl" aria-labelledby="contained-modal-title-vcenter" centered dialogClassName="border-radius-2">
                    <div className="ModalWrapper55" id="ModalWrapper">
                        <div style={{ width: "100%", height: "100%", objectFit: "cover", borderRadius: "20px" }} id="login_img5">
                            <img className="ModalImg5" src={photo} alt="" />
                                <div className="logo-img-overlay">
                                    <img src={websettings && websettings.web_header_logo} alt="" id="logo5" />
                                </div>
                            <div className="logo-text5">
                                <h4>{translate("beautifulltheme")}</h4>
                                <p> {translate("bestinvestment")}</p>
                            </div>
                        </div>

                        <div id="modal-content">
                            <Modal.Header closeButton>
                                <Modal.Title id="contained-modal-title-vcenter">{translate("login")}</Modal.Title>
                            </Modal.Header>
                            <Modal.Body >
                                <div className="AC">
                                    <div className="">
                                        <h5>
                                            <strong>{translate("otp-sent")}  </strong>
                                        </h5>
                                        <div id="Welcom" style={{ fontSize: "14px" }}>
                                            {" "}
                                            {props.phonenum}{" "}
                                        </div>
                                    </div>
                                    <form onSubmit={(e) => { e.preventDefault();}}>
                                        <div className="mb-3 my-3">
                                            <OTPInput className="otp-container" value={OTP} onChange={setOTP} autoFocus OTPLength={6} otpType="number" disabled={false} secure />
                                            <p className="error-msg">{error}</p>
                                            <div>
                                                <button onClick={resendOTP} id="resendbutton" className="btn ps-0">
                                                {translate("resendLbl")}
                                                </button>
                                            </div>
                                        </div>

                                        <div className="py-3">
                                            <button type="submit" className="btn   btn-lg  w-100" id="submitbutton" onClick={(e) => submitOTP(e)}>
                                            {translate("submitBtn")}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </Modal.Body>
                        </div>
                    </div>
                </Modal>
                <div id="recaptcha-container" style={{ display: "none" }}></div>
            </div>
        </>
    );
}

export default OTPmodal2;
