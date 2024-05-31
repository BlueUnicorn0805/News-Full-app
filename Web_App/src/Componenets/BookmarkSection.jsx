import React, { useEffect, useRef, useState } from "react";
import bookmarkIMG from "../images/bookmark.png";
import { FiCalendar } from "react-icons/fi";
import { BsTrash } from "react-icons/bs";
import BreadcrumbNav from "./BreadcrumbNav";
import { getbookmarkApi, setbookmarkApi } from "../store/actions/campaign";
import { convertToSlug, translate } from "../utils";
import Skeleton from "react-loading-skeleton";
import { useNavigate } from "react-router-dom";

const BookmarkSection = () => {
    const navigate = useNavigate();
    const [Data, setData] = useState([]);
    const [isLoading, setIsLoading] = useState(true);

    const initialData = useRef([]);

    useEffect(() => {
      initialData.current = Data;
    }, [Data]);

    // get bookmark
    useEffect(() => {
      getbookmarkApi(
        "0",
        "",
          (response) => {
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
    }, []);

    // set bookmark for remove bookmark
    const setbookmarkData = (e, newsid, status) => {
      e.preventDefault();
      setbookmarkApi(
        newsid,
        status,
          (response) => {
              setData(Data.filter((item) => item.news_id !== newsid));
              setIsLoading(false);
        },
        (error) => {
            setIsLoading(false);
            if (error === "No Data Found") {
                setData("");
            }
        }
      );
    };


    return (
        <>
            <BreadcrumbNav SecondElement={translate("bookmarkLbl")} ThirdElement="0" />

            <div id="bs-main" className="py-5 bookmark_page">
                    <div id="bs-content" className="container">
                        <div className="row">
                        {isLoading ? (
                            // Show skeleton loading when data is being fetched
                            <div className="col-12 loading_data">
                                <Skeleton height={20} count={22} />
                            </div>
                        ) : Data.length > 0 ? (
                            Data.map((element) => (
                                <div className="col-md-6 col-lg-4 col-12" key={element.id}>
                                    <div id="bs-card" className="card">
                                        <div className="bs_image_card">
                                            <img id="bs-card-image" src={element.image} className="card-img" alt="..." onClick={() => navigate(`/news/${element.news_id}/${element.category_id}`)} />
                                            <button id="bs-btnBookmark" className="btn" onClick={(e) => setbookmarkData(e, element.news_id, "0")}>
                                                <BsTrash id="bs-bookmark-logo" size={18} />
                                            </button>
                                        </div>
                                        <div id="bs-card-body" className="card-body">
                                            <button id="bs-btnCatagory" className="btn btn-sm" type="button" onClick={() => navigate(`/news/${element.news_id}/${element.category_id}`)}>
                                                {element.category_name}
                                            </button>
                                            <h5 id="bs-card-title" className="card-title" onClick={() => navigate(`/news/${element.news_id}/${element.category_id}`)}>
                                                {element.title}
                                            </h5>
                                            <p id="bs-card-date">
                                                <FiCalendar size={18} id="bs-logoCalendar" />
                                                {element.date.slice(0, 10)}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            ))
                        ) : (
                            // Show "No data found" message if no data is available
                            <div className="col-12 no_data mt-5">
                                <div id="bs-no-main">
                                    <img id="bs-no-image" src={bookmarkIMG} alt="" />
                                    <p id="bs-no-title">
                                        <b>{translate("addbookmark")}</b>
                                    </p>
                                    <p id="bs-no-text">{translate("dontforgetbookmark")}</p>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

export default BookmarkSection;
