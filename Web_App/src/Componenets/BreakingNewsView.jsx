import React from "react";
import { useState } from "react";
import { Link } from "react-router-dom";
import { useEffect } from "react";
import BreadcrumbNav from "./BreadcrumbNav";
import { useSelector } from "react-redux";
import { selectCurrentLanguage } from "../store/reducers/languageReducer";
import { getbreakingNewsApi } from "../store/actions/campaign";
import Skeleton from "react-loading-skeleton";
import { convertToSlug, translate } from "../utils";
import no_image from "../images/no_image.jpeg";

function BreakingNewsView() {
    const [Data, setData] = useState([]);
    const [loading, setLoading] = useState(true);

    const currentLanguage = useSelector(selectCurrentLanguage);
    useEffect(() => {
        getbreakingNewsApi(
            (response) => {
                const responseData = response.data;
                setData(responseData);
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

    return (
        <>
            <BreadcrumbNav SecondElement={translate("breakingNewsLbl")} ThirdElement="0" />
            <div id="BNV-main">
                <div id="BNV-content" className="container">
                    {loading ? (
                        <div>
                            <Skeleton height={200} count={3} />
                        </div>
                    ) : (
                        <div className="row my-5">
                            {Data.length > 0 ? (
                                Data.map((element) => (
                                    <div className="col-md-4 col-12" key={element.id}>
                                        <Link id="Link-all" to={`/breaking-news/${element.id}`}>
                                            <div id="BNV-card" className="card">
                                                <img id="BNV-card-image" src={element.image ? element.image : no_image} className="card-img" alt="..." />
                                                <div id="BNV-card-body" className="card-body">
                                                    {/* <button id='BNV-btnCatagory' className='btn btn-sm' type="button" >{element.category_name}</button> */}
                                                    <h5 id="BNV-card-title" className="card-title">
                                                        {element.title.slice(0, 150)}...
                                                    </h5>
                                                    {/* <p id="BNV-card-date"><FiCalendar size={18} id="BNV-logoCalendar" />{element.date.slice(0, 10)}</p> */}
                                                    {/* <Link id='btnBNVRead' className='btn overlay' type="button" to="/news-view" ><IoArrowForwardCircleSharp size={50}/></Link> */}
                                                </div>
                                            </div>
                                        </Link>
                                    </div>
                                ))
                            ) : (
                                <div className="text-center my-5">{translate("nodatafound")}</div>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}

export default BreakingNewsView;
