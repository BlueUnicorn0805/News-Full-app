import React, { useState } from "react";
import { useSelector } from "react-redux";
import { selectUser, updateProfileData, updateProfileImage } from "../store/reducers/userReducer";
import { FaCamera } from "react-icons/fa";
import { toast } from "react-toastify";
import { profileimgError, translate } from "../utils";
import BreadcrumbNav from "./BreadcrumbNav";
import validator from "validator";

const ProfileUpdate = () => {
    const userData = useSelector(selectUser);
    const [isMobileValid, setIsMobileValid] = useState(true); // State to track mobile number validity
    const [isEmailValid, setIsEmailValid] = useState(true); // State to track email address validity

    const [profileData, setProfileData] = useState({
        name: userData ? userData.data?.name : "",
        mobile: userData? userData.data?.mobile : "",
        email: userData ? userData.data?.email : "",
    });

    const handleChange = (e) => {
        const field_name = e.target.name;
        const field_value = e.target.value;
        setProfileData((values) => ({ ...values, [field_name]: field_value }));
    };

    // update profile button
    const formDetails = (e) => {
        e.preventDefault();
        
        if(profileData?.name === ""){
            toast.error("Please enter your name");
            return;
        }

        // Validate email only when it's not empty
        if (!isEmailValid) {
            toast.error("Enter a valid email address");
            return;
        }

         // Validate mobile only when it's not empty
         if (!isMobileValid) {
            toast.error("Enter a valid phone number");
            return;
        }
    
        if (!JSON.parse(process.env.REACT_APP_DEMO)) {
            updateProfileData(
                profileData.name,
                profileData.mobile,
                profileData.email,
                (success) => {
                    toast.success("successfully updated");
                },
                (error) => {
                    toast.error(error);
                }
            );
        } else {
            toast.error(translate("Profile update is not allowed in demo version."));
        }
    };

    // handle image change
    const handleImageChange = (e) => {
        e.preventDefault();
        const selectedFile = e.target.files[0];

        // Check if a file is selected
        if (!selectedFile) {
            return;
        }

        // Check if the selected file type is an image
        if (!selectedFile.type.startsWith("image/")) {
            toast.error("Please select an image file.");
            return;
        }
        
        if (!JSON.parse(process.env.REACT_APP_DEMO)) {
            updateProfileImage(
                e.target.files[0],
                () => {},
                (error) => {
                    console.log(error);
                }
            );
        }
    }

    // validate
    const validateNumber = (e) => {
        const enteredValue = e.target.value;

        // Check if the entered value is an empty string
        if (enteredValue === "") {
            // If the mobile number is removed, set the 'mobile' field in 'profileData' to null
            setProfileData((prevState) => ({ ...prevState, mobile: null }));
            setIsMobileValid(true); // Reset the mobile number validity when it's empty
        } else {
            // Otherwise, update the 'mobile' field with the entered value
            setProfileData((prevState) => ({ ...prevState, mobile: enteredValue }));

            // Validate mobile if the entered value is not empty
            setIsMobileValid(validator.isMobilePhone(enteredValue)); // Set the mobile number validity flag
        }
    };


    const validateEmail = (e) => {
        const enteredValue = e.target.value;

        // Check if the entered value is an empty string
        if (enteredValue === "") {
            // If the email address is removed, set the 'email' field in 'profileData' to null
            setProfileData((prevState) => ({ ...prevState, email: null }));
            setIsEmailValid(true); // Reset the email address validity when it's empty
        } else {
            // Otherwise, update the 'email' field with the entered value
            setProfileData((prevState) => ({ ...prevState, email: enteredValue }));

            // Validate email if the entered value is not empty
            setIsEmailValid(validator.isEmail(enteredValue)); // Set the email address validity flag
        }
    };


    return (
        <>
            <BreadcrumbNav SecondElement={translate("profile")} ThirdElement="0" />
            <section className="profile py-5 bg-white">
                <div className="container">
                    <div className="row">
                        <div className="col-md-6">
                            <img className="create-img" src={process.env.PUBLIC_URL + "/images/Profile.svg"} alt="profile" />
                        </div>
                        <div className="col-md-6">
                            <div className="profile_content">
                                <div className="prop__image">
                                    <img src={userData.data && userData.data.profile ? userData.data.profile : process.env.PUBLIC_URL + "/images/user.svg"} onError={profileimgError} alt="profile" id="user_profile" />
                                    <div className="select__profile">
                                        <input type="file" name="image" id="file" accept="image/*" onChange={(e) => handleImageChange(e)} />
                                        <label htmlFor="file">
                                            {" "}
                                            <em>
                                                <FaCamera />
                                            </em>
                                        </label>
                                        <input type="text" className="form-control" accept="image/*" placeholder="Upload File" id="file1" name="myfile" disabled hidden />
                                    </div>
                                </div>
                                <div className="profile_name">
                                    <label htmlFor="name">{translate("your-name")}</label>
                                    <input type="text" name="name" id="name" placeholder="Enter Your Name" className="profile_input" defaultValue={userData.data && userData.data.name} onChange={(e) => handleChange(e)} required />
                                </div>
                                <div className="profile_name email">
                                    <label htmlFor="email">{translate("emailLbl")}</label>
                                    {userData && userData.mobileLoginType ? (
                                        <input type="text" name="email" id="email" className="profile_input" placeholder="Enter Your Email" defaultValue={userData.data && userData.data.email} onChange={(e) => validateEmail(e)} />
                                    ) : (
                                        <input type="text" name="email" id="email" className="profile_input" placeholder={userData.data && userData.data.email} readOnly />
                                    )}
                                </div>
                                <div className="profile_name mobile">
                                    <label htmlFor="mobile">{translate("mobileLbl")}</label>
                                    {userData && userData.mobileLoginType ? (
                                        <input type="number" name="mobile" id="mobile" className="profile_input" min="0" placeholder={userData.data && userData.data.mobile} readOnly />
                                    ) : (
                                        <input type="number" name="mobile" id="mobile" className="profile_input" min="0" defaultValue={userData.data && userData.data.mobile} onChange={(e) => validateNumber(e)} />
                                    )}
                                </div>
                                <button type="button" className="profile_submit" onClick={(e) => formDetails(e)}>
                                    {translate("update-profile")}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default ProfileUpdate;
