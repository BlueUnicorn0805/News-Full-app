import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import React from "react";
import { getbreakingNewsApi } from "../store/actions/campaign";
import { useSelector } from "react-redux";
import { selectCurrentLanguage } from "../store/reducers/languageReducer";
import { convertToSlug, translate } from "../utils";
import Skeleton from "react-loading-skeleton";

function RelatedBreakingNews(props) {
    const [Data, setData] = useState([]);
    const currentLanguage = useSelector(selectCurrentLanguage);
    const [loading, setLoading] = useState(true);
    useEffect(() => {
        getbreakingNewsApi(
            (response) => {
                const responseData = response.data;
                const filteredData = responseData.filter((element) => element.id !== props.id);
                setData(filteredData);
                setLoading(false)
            },
            (error) => {
                if (error === "No Data Found") {
                    setData("");
                    setLoading(false);
                }
            }
        );
    }, [currentLanguage,props.id]);

    return (
        <div>
            {loading ? (
                <div>
                    <Skeleton height={200} count={3} />
                </div>
            ) : Data.length > 0 ? (
                <div id="rbn-main">
                    <nav id="rbn-cat-nav" className="navbar">
                        <h4 id="nav-logo" className="mb-0">
                            <b>{translate("related-news")}</b>
                        </h4>
                    </nav>
                    {Data &&
                        Data.map((element) => (
                            <div key={element.id}>
                                <Link id="Link-all" to={`/breaking-news/${element.id}`}>
                                    <div id="rbn-card" className="card">
                                        <img id="rbn-image" src={element.image} className="card-img-top" alt="..." />
                                        <div id="rbn-card-body" className="rbn-card-body">
                                            <Link id="btnrbnCatagory" className="btn btn-sm" type="button">
                                                {translate("breakingnews")}
                                            </Link>
                                            <h6 id="rbn-card-text" className="card-text">
                                                {element.title.slice(0, 40)}...
                                            </h6>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        ))}
                </div>
            ) : null}
        </div>
    );
}

export default RelatedBreakingNews;
