import photo from "../images/Login.jpg";
import React, { useState, useEffect } from "react";
import Modal from "react-bootstrap/Modal";
import { getAuth, sendPasswordResetEmail } from "firebase/auth";
import { translate } from "../utils";
import { toast } from "react-toastify";
import { useSelector } from "react-redux";
import { webSettingsData } from "../store/reducers/websettingsReducer";

function ForgotPasswordTwo(props) {
    const initialValues = { email: "", password: "" };
    const [formValues, setFormValues] = useState(initialValues);
    const [formErrors, setFormErrors] = useState(
        "",
        setTimeout(() => {
            if (formErrors !== "") setFormErrors("");
        }, 5000)
    );
    const [isSubmit, setIsSubmit] = useState(false);

    const websettings = useSelector(webSettingsData);


    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormValues({ ...formValues, [name]: value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setFormErrors(validate(formValues));
        setIsSubmit(true);
        const auth = getAuth();
        await sendPasswordResetEmail(auth, formValues.email)
            .then((userCredential) => {
                toast.success("Email sent Succesfully")
                // ...
                props.onHide();
                props.setLoginModalShow(true);
            })
            .catch((error) => {
                console.log(error);
                // ..
            });
    };
    useEffect(() => {
        if (Object.keys(formErrors).length === 0 && isSubmit); // eslint-disable-next-line
    }, [formErrors]); // eslint-disable-next-line
    const validate = (values) => {
        const errors = {};
        const regex = /^[^\s@]+@[^s]+\.[^\s@]{2,}$/i;
        if (!values.email) {
            errors.email = "Email is required!";
        } else if (!regex.test(values.email)) {
            errors.email = "Enter a Valid EMail";
        }

        return errors;
    };
    return (
        <div>
            <Modal {...props} size="xl" aria-labelledby="contained-modal-title-vcenter" centered dialogClassName="border-radius-2">
                <div className="ModalWrapper" id="ModalWrapper11">
                    <div className="forgot-password" style={{ width: "100%", height: "100%", objectFit: "cover", borderRadius: "20px" }} id="login_img2">
                        <img className="ModalImg" id="ModalImg2" src={photo} alt="" />
                        <div className="logo-img-overlay">
                            <img id="NewsLogo" src={websettings && websettings.web_header_logo} alt="" />
                        </div>
                        <div className="logo-text2">
                            <h4> {translate("beautifulltheme")}</h4>
                            <p> {translate("bestinvestment")}</p>
                        </div>
                    </div>

                    <div id="modal-content2">
                        <Modal.Header closeButton>
                            <Modal.Title id="contained-modal-title-vcenter"> {translate("forgotPassLbl")}</Modal.Title>
                        </Modal.Header>
                        <Modal.Body style={{ marginTop: "12%" }}>
                            <div className="">
                                <div className="AC">
                                    <div className="welcom-back2">
                                        <h5 className="mb-3">
                                            <strong>{translate("enteremail")} </strong>
                                        </h5>
                                        <div id="Welcom" className="mb-2">
                                        {translate("createnewpassword")}
                                        </div>
                                    </div>
                                    <form className="my-2 " onSubmit={handleSubmit}>
                                        <div className="mb-3">
                                            <input
                                                type="text"
                                                className="form-control email-input"
                                                name="email"
                                                id="exampleInputEmail1"
                                                aria-describedby="emailHelp"
                                                placeholder="Email Address"
                                                value={formValues.email}
                                                onChange={handleChange}
                                            />
                                            <p className="error-msg"> {formErrors.email}</p>
                                        </div>

                                        <div className="py-3">
                                            <button type="submit" className="btn   btn-lg  w-100" id="submitbutton">
                                            {translate("submitBtn")}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </Modal.Body>
                    </div>
                </div>
            </Modal>
        </div>
    );
}

export default ForgotPasswordTwo;
