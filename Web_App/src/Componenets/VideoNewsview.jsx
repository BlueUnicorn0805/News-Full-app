import React from "react";
import { useState } from "react";
import { Link, useParams } from "react-router-dom";
import { useEffect } from "react";
import { BsFillPlayFill } from "react-icons/bs";
import VideoPlayerModal from "./VideoPlayerModal";
import { FiCalendar } from "react-icons/fi";
import { getfeaturesectionbyidApi } from "../store/actions/campaign";
import { useSelector } from "react-redux";
import { selectCurrentLanguage } from "../store/reducers/languageReducer";
import Skeleton from "react-loading-skeleton";
import {translate } from "../utils";
import BreadcrumbNav from "./BreadcrumbNav";
import no_image from "../images/no_image.jpeg";

function VideoNewsview() {
    const [Data, setData] = useState([]);
    const [Video_url, setVideo_url] = useState();
    const [modalShow, setModalShow] = useState(false);
    const [loading, setLoading] = useState(true);
    const [typeUrl,setTypeUrl] = useState(null);
    const { slug, vid } = useParams();
    const catid = vid;
    const categoryName = slug;
    const currentLanguage = useSelector(selectCurrentLanguage);

    useEffect(() => {
        getfeaturesectionbyidApi(
            catid,
            "",
            "10",
            (response) => {
                setData(response.data);
                setLoading(false);
            },
            (error) => {
                if (error === "No Data Found") {
                    setData("");
                    setLoading(false);
                }
            }
        );
    }, [catid, currentLanguage]);

    function handleLiveNewsVideoUrl(url) {
        setModalShow(true);
        setVideo_url(url);
    }

    const TypeUrl = (type) => {
        setTypeUrl(type)
    }

    return (
        <>
            <BreadcrumbNav SecondElement={categoryName} ThirdElement="0" />
            <div className="py-5 video_section_all">
            <div className="container">
                {loading ? (
                    <div>
                        <Skeleton height={200} count={3} />
                    </div>
                ) : (
                    <div className="row">
                        {Data && Data[0].videos?.length > 0 ? (
                            Data[0].videos.map((element) => (
                                <div className="col-md-4 col-12" key={element.id} >

                                    <div id="vnv-card" className="card" onClick={() => {handleLiveNewsVideoUrl(element.content_value); TypeUrl(element.type)}}>
                                    <img id="vnv-card-image" src={element.image ? element.image : no_image} className="card-img" alt="..." />
                                        <Link className="card-image-overlay" id="vnv-btnVideo">
                                            <BsFillPlayFill id="vnv-btnVideo-logo" className="pulse" fill="white" size={50} />
                                        </Link>

                                        <div id="vnv-card-body" className="card-body">
                                            {/* <button id='vnv-btnCatagory' className='btn btn-sm' type="button" >{element.category_name}</button> */}
                                            <h5 id="vnv-card-title" className="card-title">
                                                {element.title}
                                            </h5>
                                            {/* <Link id='btnvnvRead' className='btn overlay' type="button" to="/news-view" ><IoArrowForwardCircleSharp size={50}/></Link> */}
                                        </div>
                                    </div>
                                    <VideoPlayerModal
                                        show={modalShow}
                                        onHide={() => setModalShow(false)}
                                        // backdrop="static"
                                        keyboard={false}
                                        url={Video_url}
                                        type_url={typeUrl}
                                    // title={Data[0].title}
                                    />

                                    {/* </Link> */}
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
    )
}

export default VideoNewsview;
