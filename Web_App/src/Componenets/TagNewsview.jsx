import React from "react";
import { FiCalendar } from "react-icons/fi";
import { useState } from "react";
import { Link, useParams } from "react-router-dom";
import { useEffect } from "react";
import { getnewsbytagApi } from "../store/actions/campaign";
import Skeleton from "react-loading-skeleton";
import { convertToSlug, translate } from "../utils";

function TagNewsview() {
    const [Data, setData] = useState([]);
    const [loading, setLoading] = useState(true);
    const { tagid } = useParams();
    const Tid = tagid;

    useEffect(() => {
        getnewsbytagApi(
            Tid,
            (response) => {
                setData(response.data);
                setLoading(false)
            },
            (error) => {
                if (error === "No Data Found") {
                    setData("");
                    setLoading(false);
                }
            }
        ); // eslint-disable-next-line
    }, []);

    // tags
    const tagSplit = (tag) => {
        let tags = tag.split(",");
        return tags;
    }

    return (
        <div className="py-5 tagview bg-white">
            <div className="container">
                <div className="row">
                    {loading ? (
                        <div>
                            <Skeleton height={200} count={3} />
                        </div>
                    ) : Data.length > 0 ? (
                        <>
                            {Data &&
                                Data.map((element) => (
                                    <div className="col-md-4 col-12" key={element.id}>
                                        <Link id="Link-all" to={`/news/${element.id}/${element.category_id}`}>
                                            <div id="ts-card" className="card">
                                                <img id="ts-card-image" src={element.image} className="card-img" alt="..." />

                                                <div id="ts-card-body" className="card-body">
                                                    <div className="tag_button">
                                                        {tagSplit(element.tag_name).map((tag, index) => (
                                                            <button
                                                                className="btn btn-sm tag-button"
                                                                type="button"
                                                                key={index}
                                                            >
                                                                {tag}
                                                            </button>
                                                        ))}
                                                    </div>

                                                    <h5 id="ts-card-title" className="card-title">
                                                        {element.title.slice(0, 150)}...
                                                    </h5>
                                                    <p id="ts-card-date">
                                                        <FiCalendar size={18} id="ts-logoCalendar" />
                                                        {element.date.slice(0, 10)}
                                                    </p>
                                                </div>
                                            </div>
                                        </Link>
                                    </div>
                                ))}
                        </>
                    ): (
                        <div className="text-center my-5">{translate("nodatafound")}</div>
                    )}
                </div>
            </div>
        </div>
    );
}

export default TagNewsview;
