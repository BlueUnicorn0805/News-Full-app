
import React, { useEffect, useState } from "react";
import { BiBell, BiUserCircle } from "react-icons/bi";
import { Link, NavLink, useNavigate } from "react-router-dom";
import Button from "react-bootstrap/Button";
import { getAuth, signOut } from "firebase/auth";
import { confirmAlert } from "react-confirm-alert";
import "react-confirm-alert/src/react-confirm-alert.css";
import Dropdown from "react-bootstrap/Dropdown";
import { loadLanguageLabels, loadLanguages, selectCurrentLanguage, selectLanguages, setCurrentLanguage } from "../store/reducers/languageReducer";
import { useSelector } from "react-redux";
import { categoriesApi, getuserbyidApi } from "../store/actions/campaign";
import { getSiblings, slideToggle, slideUp, getClosest, isLogin, translate, truncateText, profileimgError } from "../utils/index";
import { logoutUser, selectUser} from "../store/reducers/userReducer";
import SignInModal from "./SignInModal";
import { toast } from "react-toastify";
import {  webSettingsData } from "../store/reducers/websettingsReducer";
import { counterData, loadNotification, loaduserNotification } from "../store/reducers/notificationbadgeReducer";
import MobilesideBar from "./MobilesideBar";
import { settingsData } from "../store/reducers/settingsReducer";
import { AiOutlineSearch } from "react-icons/ai";
import { SetSearchPopUp } from "../store/stateSlice/clickActionSlice";
import { store } from "../store/store";

const Newsbar = () => {

    const userData = useSelector(selectUser);

    const auth = getAuth();
    const [Data, setData] = useState([]);
    const [modalShow, setModalShow] = useState(false);
    const [islogout, setIsLogout] = useState(false); // eslint-disable-next-line
    const [isloginloading, setisloginloading] = useState(true); // eslint-disable-next-line
    const [profileModal, setProfileModal] = useState(false);
    const [isuserRole, setisuserRole] = useState(false);

    const navigate = useNavigate();

    const counterBadgeData = useSelector(counterData);

    const languagesData = useSelector(selectLanguages);

    const currentLanguage = useSelector(selectCurrentLanguage);

    const websettings = useSelector(webSettingsData);

    const settings = useSelector(settingsData);


    useEffect(() => {
        // get categories
        categoriesApi(
            "0",
            "16",
            currentLanguage.id,
            (response) => {
                setData(response.data);
            },
            (error) => {
                if (error === "No Data Found") {
                    setData("");
                }
            }
        );


        // language laod
        loadLanguages(
            (response) => {
                if (currentLanguage.code == null) {// eslint-disable-next-line
                    let index = response && response.data.filter((data) => {
                        if (data.code === settings.default_language[0].code) {
                            return { code: data.code, name: data.language, id: data.id };
                        }
                    });

                    setCurrentLanguage(index[0].language, index[0].code, index[0].id);
                }
            },
            (error) => {
                console.log(error);
            }
        );

// eslint-disable-next-line
    }, [currentLanguage]);

    // language change
    const languageChange = (name, code, id) => {
        loadLanguageLabels(code);
        setCurrentLanguage(name, code, id);
    };

    useEffect(() => {
        if (userData.data !== null) {
            setIsLogout(true);
            setisloginloading(false);
        } else {
            setIsLogout(false);
            setisloginloading(true);
        } // eslint-disable-next-line
    }, []);

    // user notification
    const getusernotification = () => {
        loaduserNotification(
            "0",
            "10",
            (response) => {
            },
            (error) => {
            }
        );
    };

    const getnotification = () => {
        loadNotification(
            "0",
            "20",
            (response) => {
            },
            (error) => {
            }
        );
    };


      useEffect(() => {
        // Make API calls here based on route change
        if (window.location.pathname === '/') {
            getusernotification();
        }else if(window.location.pathname === '/notification' || window.location.pathname === '/persnol-notification') {
            getusernotification();
        }else if (window.location.pathname === '/news-notification') {
            getnotification();
        }// eslint-disable-next-line
      }, [window.location.pathname,isLogin()]);

    const logout = () => {
        handleClose();

        confirmAlert({
            title: "Logout",
            message: "Are you sure to do this.",
            buttons: [
                {
                    label: "Yes",
                    onClick: () => {
                        signOut(auth)
                            .then(() => {
                                logoutUser();
                                setIsLogout(false);
                                navigate("/");
                            })
                            .catch((error) => {
                                toast.error(error);
                                // An error happened.
                            });
                    },
                },
                {
                    label: "No",
                    onClick: () => {},
                },
            ],
        });
    };

    const [show, setShow] = useState(false);

    const handleClose = () => setShow(false);
    const handleShow = () => {
        setShow(true);
    };

    const onClickHandler = (e) => {
        const target = e.currentTarget;
        const parentEl = target.parentElement;
        if (parentEl?.classList.contains("menu-toggle") || target.classList.contains("menu-toggle")) {
            const element = target.classList.contains("icon") ? parentEl : target;
            const parent = getClosest(element, "li");
            const childNodes = parent.childNodes;
            const parentSiblings = getSiblings(parent);
            parentSiblings.forEach((sibling) => {
                const sibChildNodes = sibling.childNodes;
                sibChildNodes.forEach((child) => {
                    if (child.nodeName === "UL") {
                        slideUp(child, 1000);
                    }
                });
            });
            childNodes.forEach((child) => {
                if (child.nodeName === "UL") {
                    slideToggle(child, 1000);
                }
            });
        }
    };

    let userName = "";

    const checkUserData = (userData) => {
        if (userData.data && userData.data.name !== "") {
            return (userName = userData.data.name);
        } else if (userData.data && userData.data.email !== "") {
            return (userName = userData.data.email);
        } else if (userData.data && (userData.data.mobile !== null || userData.data.mobile !== "")) {
            return (userName = userData.data.mobile);
        }
    };


    // set rtl
    const selectedLang = languagesData && languagesData.find(lang => lang.code === currentLanguage.code);
    useEffect(() => {
        if (selectedLang && selectedLang.isRTL === "1") {
            document.documentElement.dir = "rtl";
            document.documentElement.lang = `${selectedLang && selectedLang.code}`;
        } else {
            document.documentElement.dir = "ltr";
            document.documentElement.lang = `${selectedLang && selectedLang.code}`;
        }
    }, [selectedLang]);

    const searchPopUp = useSelector((state) => state.clickAction.searchPopUp);
    const actionSearch = () => {
      store.dispatch(SetSearchPopUp(!searchPopUp));
    };


    // user roles
    useEffect(() => {
        getuserbyidApi((response) => {
            const userRole = response.data;
            const Role = userRole.map((elem) => elem.role);
            if (Role[0] !== "0") {
                setisuserRole(true)
            }
         },(error)=>console.log(error));
    },[])

    return (
        <>
            <nav className="Newsbar">
                <div className="container">
                    <div className="navbar_content">
                        <div id="News-logo" className="News-logo">
                            <NavLink to="/" activeclassname="active" exact="true">
                                <img id="NewsLogo" src={websettings && websettings.web_header_logo} alt="logo" />
                            </NavLink>
                        </div>

                        <div className="Manu-links">
                            <ul className="">
                                <li id="NavHover" className="nav-item">
                                    <b>
                                        <NavLink id="nav-links" activeclassname="active" exact="true" className="link-color" aria-current="page" to="/" >
                                            {translate("home")}
                                        </NavLink>
                                    </b>
                                </li>
                                {settings && settings.live_streaming_mode === "1" ?
                                    <li id="NavHover" className="nav-item">
                                        <b>
                                            <NavLink id="nav-links" activeclassname="active" exact="true" className="link-color" aria-current="page" to="/live-news">
                                                {translate("livenews")}
                                            </NavLink>
                                        </b>
                                    </li>
                                : null}
                                {settings && settings.breaking_news_mode === "1" ?
                                    <li id="NavHover" className="nav-item">
                                        <b>
                                            <NavLink id="nav-links" activeclassname="active" exact="true" className="link-color" aria-current="page" to="/breaking-news-view">
                                                {translate("breakingnews")}
                                            </NavLink>
                                        </b>
                                    </li>
                                : null}
                                <li id="NavHover" className="nav-item">
                                    <b>
                                        <NavLink id="nav-links" activeclassname="active" exact="true" className="link-color" aria-current="page" to="/more-pages">
                                            {translate("More Pages")}
                                        </NavLink>
                                    </b>
                                </li>
                                <li id="Nav-btns" className="d-flex">
                                    {isLogin() && checkUserData(userData) ? (
                                        <Dropdown>
                                            <Dropdown.Toggle id="btnSignIn" className="me-2">
                                                <img className="profile_photo" src={userData.data && userData.data.profile } onError={profileimgError} alt="profile"/>
                                                {truncateText(userName,10)}
                                            </Dropdown.Toggle>

                                            <Dropdown.Menu style={{ backgroundColor: "#1A2E51" }}>
                                                <Dropdown.Item id="btnLogout">
                                                    <Link id="btnBookmark" to="/bookmark">
                                                        {translate("bookmark")}
                                                    </Link>
                                                </Dropdown.Item>
                                                <Dropdown.Item id="btnLogout">
                                                    <Link id="btnBookmark" to="/user-based-categories">
                                                        {translate("managePreferences")}
                                                    </Link>
                                                </Dropdown.Item>

                                                {isuserRole ?
                                                    <>
                                                        <Dropdown.Item id="btnLogout">
                                                            <Link id="btnBookmark" to="/create-news">
                                                                {translate("createNewsLbl")}
                                                            </Link>
                                                        </Dropdown.Item>

                                                        <Dropdown.Item id="btnLogout">
                                                            <Link id="btnBookmark" to="/manage-news">
                                                                {translate("manageNewsLbl")}
                                                            </Link>
                                                        </Dropdown.Item>
                                                    </>
                                                : null}
                                                <Dropdown.Item id="btnLogout">
                                                    <Link id="btnBookmark" to="/profile-update">
                                                        {translate("update-profile")}
                                                    </Link>
                                                </Dropdown.Item>
                                                {/*<Dropdown.Item id='btnLogout' onClick={changePassword}>*/}
                                                {/*    Change Password*/}
                                                {/*</Dropdown.Item>*/}
                                                <Dropdown.Divider />
                                                <Dropdown.Item onClick={logout} id="btnLogout" className="">
                                                {translate("logout")}
                                                </Dropdown.Item>
                                            </Dropdown.Menu>
                                        </Dropdown>
                                    ) : (
                                        <Button variant="danger" onClick={() => setModalShow(true)} id="btnSignIn" className="me-2" type="button">
                                            <BiUserCircle size={23} id="btnLogo" />
                                            {translate("loginLbl")}
                                        </Button>
                                    )}

                                    {/* notifiaction */}
                                    {isLogin() ? (
                                        <Link to="/notification" id="btnNotification" type="button" className="btn">
                                            <BiBell size={23} /><span className="noti_badge_data">{counterBadgeData && counterBadgeData.counter}</span>
                                        </Link>
                                    ) : null}

                                    {/* searchbar */}
                                    <Link id="btnNotification" type="button" className="btn ms-2" onClick={actionSearch}>
                                        <AiOutlineSearch size={23} />
                                    </Link>

                                </li>
                            </ul>

                            <SignInModal setIsLogout={setIsLogout} setisloginloading={setisloginloading} show={modalShow} setLoginModalShow={setModalShow} onHide={() => setModalShow(false)} />
                        </div>
                        <div className="hamburger-manu">
                            {["end"].map((placement, idx) => (
                                <MobilesideBar key={idx} isuserRole={ isuserRole} languageChange={languageChange } placement={placement} name={placement} logout={logout} onClickHandler={onClickHandler} Data={Data} modalShow={modalShow} setModalShow={setModalShow} islogout={islogout} setIsLogout={setIsLogout} handleShow={handleShow} show={show} handleClose={handleClose } ProfileModal={setProfileModal} />
                            ))}
                        </div>
                    </div>
                </div>
            </nav>
        </>

    );
};

export default Newsbar;
