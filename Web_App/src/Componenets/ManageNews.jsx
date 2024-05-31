import React, { useEffect, useState } from "react";
import { deleteNewsApi, getnewsApi } from "../store/actions/campaign";
import { useSelector } from "react-redux";
import { selectUser } from "../store/reducers/userReducer";
import BreadcrumbNav from "./BreadcrumbNav";
import { convertToSlug, translate } from "../utils";
import Skeleton from "react-loading-skeleton";
import { useNavigate } from "react-router-dom";
import { loadManageToEdit } from "../store/reducers/createNewsReducer";
import { toast } from "react-toastify";
import { selectCurrentLanguage } from "../store/reducers/languageReducer";

const ManageNews = () => {
    const navigate = useNavigate();
    const userData = useSelector(selectUser);
    const [Data, setData] = useState([]);
    const [loading, setLoading] = useState(true);
    const currentLanguage = useSelector(selectCurrentLanguage);

    useEffect(() => {
        getnewsApi(
            "",
            "",
            userData.data.id,
            "",
            (res) => {
                setData(res.data);
                setLoading(false);
            },
            (error) => {
                if (error === "No Data Found") {
                    setData("");
                    setLoading(false);
                }
            }
        );
    }, [currentLanguage]);

    // type return
    const typeReturn = (type) => {
        if (type === "video_upload") {
            return translate("videoUploadLbl");
        } else if (type === "standard_post") {
            return translate("stdPostLbl");
        } else if (type === "video_youtube") {
            return translate("videoYoutubeLbl");
        } else if (type === "video_other") {
            return translate("videoOtherUrlLbl");
        }
    };

    const editNews = (data) => {
        loadManageToEdit(data)
        navigate(`/edit-news`);
    }

    const deleteNews = (data) => {
        deleteNewsApi(data.id, (res) => {
            toast.success(res.message);
            const updatedData = Data.filter((item) => item.id !== data.id);
            setData(updatedData);
        }, (err) => {
            toast.error(err.message);
        })
    }

    return (
        <>
            <BreadcrumbNav SecondElement={translate("manageNewsLbl")} ThirdElement="0" />

            <div className="manage_news bg-white py-5">
                <div className="container">
                    <div className="row">
                        {loading ? (
                            <div>
                                <Skeleton height={200} count={3} />
                            </div>
                        ) : (
                            <>
                                {Data.length > 0 ? (
                                    Data.map((element, id) => (
                                        <div className=" col-xl-4 col-md-6 col-12" key={id}>
                                            <div className="manage-data">
                                                <div className="manage-card">
                                                    <div className="manage-img">
                                                        <img src={element.image} alt="" onClick={() => navigate(`/news/${element.id}/${element.category_id}`)} />
                                                    </div>
                                                    <div className="manage-title">
                                                        <p onClick={() => navigate(`/news/${element.id}/${element.category_id}`)}>{element.category_name}</p>
                                                    </div>
                                                    <div className="manage-date">
                                                        <p>{new Date(element.date).toLocaleTimeString([], { hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true })}</p>
                                                    </div>
                                                </div>
                                                <div className="manage-right">
                                                    <div className="manage-title">
                                                        <p className="mb-0" onClick={() => navigate(`/news/${element.id}/${element.category_id}`)}>{element.title}</p>
                                                    </div>
                                                    <div className="manage_type">
                                                        <p className="mb-1">
                                                            {translate("contentTypeLbl") } : <span>{typeReturn(element.content_type)}</span>
                                                        </p>
                                                    </div>
                                                    <div className="manage-tag">
                                                        {element.tag_name.split(",").map((tagName, index) => (
                                                            <p key={index}  onClick={() => navigate(`/tag/${element.tag_id}`)}>{tagName}</p>
                                                        ))}
                                                    </div>
                                                    <div className="manage-buttons">
                                                        <div className="manage-button-edit">
                                                            <button className="btn btn-dark" onClick={(e) => editNews(element)}>{translate("editLbl") }</button>
                                                        </div>
                                                        <div className="manage-button-delete">
                                                            <button className="btn btn-dark" onClick={(e) => deleteNews(element)}>{translate("deleteTxt") }</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))
                                ) : (
                                    <div className="text-center my-5">{translate("nodatafound")}</div>
                                )}
                            </>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
};

export default ManageNews;
