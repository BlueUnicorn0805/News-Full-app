import React, { useState, useEffect, useRef } from "react";
import { FiTrash2 } from "react-icons/fi";
import { Link } from "react-router-dom";
import BreadcrumbNav from "./BreadcrumbNav";
import { deleteusernotificationApi } from "../store/actions/campaign";
import { translate } from "../utils";
import { toast } from "react-toastify";
import Skeleton from "react-loading-skeleton";
import { MdMessage } from "react-icons/md";
import { IoMdThumbsUp } from "react-icons/io";
import { loaduserNotification } from "../store/reducers/notificationbadgeReducer";
import { selectCurrentLanguage } from "../store/reducers/languageReducer";
import { useSelector } from "react-redux";
import ReactPaginate from "react-paginate";

function Notification() {
    const [Data, setData] = useState([]);
    const [isLoading, setIsLoading] = useState(true);

    const initialData = useRef([]);

    const currentLanguage = useSelector(selectCurrentLanguage);

    const [totalLength, setTotalLength] = useState(0);
    const [offsetdata, setOffsetdata] = useState(0);
    const limit = 6;


    useEffect(() => {
      initialData.current = Data;
    }, [Data]);



    useEffect(() => {
        loaduserNotification(
            offsetdata.toString(), limit.toString(),
            (response) => {
                setTotalLength(response.total)
                setData(response.data);
                setIsLoading(false);
            },
            (error) => {
                setIsLoading(false);
                if (error === "No Data Found") {
                    setData("");
                }
            }
        );
    }, [currentLanguage, offsetdata]);

    const handlePageChange = (selectedPage) => {
        const newOffset = selectedPage.selected * limit;
        setOffsetdata(newOffset);
    };


    const handleDeleteComment = (e, id) => {
        e.preventDefault()
        deleteusernotificationApi(
            id,
            (response) => {
                 // Remove the deleted notification from the state
                setData(prevData => prevData.filter(notification => notification.id !== id));
                toast.success(response.message)
                setIsLoading(false);
                loaduserNotification("0","10",()=>{},()=>{})
            },
            (error) => {
                setIsLoading(false);
                if (error === "No Data Found") {
                    setData("");
                }
            }

        );
    };

    // Function to format the date as "day Month year"
    const formatDate = (dateString) => {
        const date = new Date(dateString);
        const day = date.getDate();
        const month = new Intl.DateTimeFormat('en-US', { month: 'long' }).format(date);
        const year = date.getFullYear();
        const hours = date.getHours();
        const minutes = date.getMinutes();
        const seconds = date.getSeconds();
        return `${day} ${month} ${year} ${hours}:${minutes}:${seconds}`;
    };

    return (
        <>
            <BreadcrumbNav SecondElement={translate("notificationLbl")} ThirdElement="0"/>

            <div className="personal_Sec py-5 bg-white">
                <div id="main-Noticard" className="container ">
                    <div className="d-flex bd-highlight mb-3">
                        <Link to="/persnol-notification" id="btnNotification1" className="btn mx-1 bd-highlight">
                            {" "}
                            {translate("personalLbl")}{" "}
                        </Link>
                        <Link to="/news-notification" id="btnNewsnoti" className="btn mx-1 bd-highlight">
                            {" "}
                            {translate("news")}
                        </Link>
                        {/* <button id='btnNotification1' className="btn  btn mx-1 ms-auto bd-highlight" onClick={handleDeleteAll} > Delete All</button> */}
                    </div>
                    <div className="my-3">
                        {isLoading ? (
                            <div className="col-12 loading_data">
                                <Skeleton height={20} count={22} />
                            </div>
                        ) : Data.length > 0 ? (
                            Data.map((element,index) => (
                                <div className="card my-3" key={index}>
                                    <div className="card-body bd-highlight" id="card-noti">
                                        {element.type === "comment_like" ? (
                                            <IoMdThumbsUp className="message_icon"/>
                                        ) : (
                                            <MdMessage className="message_icon"/>
                                        )}
                                        <div className="Noti-text">
                                            <p className="bd-highlight bd-title"> {element.message}</p>
                                            <p className="bd-highlight mb-0">{formatDate(element.date)}</p>
                                        </div>

                                        <div className="iconTrash ms-auto bd-highlight">
                                            <button className="btn mt-2" id="btntrash" onClick={(e) => handleDeleteComment(e,element.id)}>
                                                <p className="mb-0">{translate("deleteTxt")}</p>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <div className="col-12 no_data mt-5">
                                <p className="text-center">{translate("nodatafound")}</p>
                            </div>
                        )}
                    </div>

                    {totalLength > 0 ?
                        <ReactPaginate
                            previousLabel={translate("previous")}
                            nextLabel={translate("next")}
                            breakLabel="..."
                            breakClassName="break-me"
                            pageCount={Math.ceil(totalLength / limit)}
                            marginPagesDisplayed={2}
                            pageRangeDisplayed={5}
                            onPageChange={handlePageChange}
                            containerClassName={"pagination"}
                            previousLinkClassName={"pagination__link"}
                            nextLinkClassName={"pagination__link"}
                            disabledClassName={"pagination__link--disabled"}
                            activeClassName={"pagination__link--active"}
                        /> : null}
                </div>
            </div>

        </>
    );
}

export default Notification;
