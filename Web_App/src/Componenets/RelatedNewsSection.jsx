import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { getnewsbycategoryApi } from "../store/actions/campaign";
import { convertToSlug, translate, truncateText } from "../utils";
import Skeleton from "react-loading-skeleton";

function RelatedNewsSection(props) {
    const [Data, setData] = useState([]);
    const catid = props.Cid;
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        getnewsbycategoryApi(
            catid,
            "",
            "0",
            "10",
            (response) => {
                // Filter out elements with the same id as props.Cid
                const filteredData = response.data.filter((element) => element.id !== props.Nid);
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
    }, [catid,props.Nid]);

    return (
        <div>
            {loading ? (
                <div>
                    <Skeleton height={200} count={3} />
                </div>
            ) : Data.length > 0 ? (
                <div id="RNews-main">
                    <nav id="RNews-cat-nav" className="navbar">
                        <h4 id="nav-logo" className="mb-0">
                            <b>{translate("related-news")}</b>
                        </h4>
                    </nav>
                    {Data &&
                        Data.map((element) => (
                            <Link id="Link-all" to={`/news/${element.id}/${element.category_id}`} key={element.id}>
                                <div id="RNews-card" className="card">
                                    <img id="RNews-image" src={element.image} className="card-img-top" alt="..." />
                                    <div id="RNews-card-body" className="RNews-card-body">
                                        <button id="btnRNewsCatagory" className="btn btn-sm" type="button">
                                            {element.category_name}
                                        </button>
                                        <h6 id="RNews-card-text" className="card-text">
                                            {truncateText(element.title,100)}
                                        </h6>
                                    </div>
                                    {
                                    }
                                </div>
                            </Link>
                        ))}
                </div>
            ) : (
                null
            )}

        </div>
    );
}

export default RelatedNewsSection;
