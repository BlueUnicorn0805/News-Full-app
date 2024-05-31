import { Link } from "react-router-dom";
import { HiOutlineArrowLongRight } from "react-icons/hi2";
import { convertToSlug, placeholderImage, translate, truncateText } from "../utils";
import { BsFillPlayFill } from "react-icons/bs";
import VideoPlayerModal from "./VideoPlayerModal";
import { useState } from "react";

const StyleFour = ({ Data }) => {

    const scrollToTop = () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    };

    const [Video_url, setVideo_url] = useState();
    const [typeUrl,setTypeUrl] = useState(null);
    const [modalShow, setModalShow] = useState(false);


    function handleVideoUrl(url) {
        setModalShow(true);
        setVideo_url(url);
    }

    const TypeUrl = (type) => {
        setTypeUrl(type)
    }

    return (
        <>

            {/* ad spaces */}
            {Data.ad_spaces && Data.id === Data.ad_spaces.ad_featured_section_id && Data.news_type === "videos" ? (
                <div className="ad_spaces">
                    <div className="container">
                        <div target="_blank" onClick={() => window.open(Data.ad_spaces.ad_url, '_blank')}>
                            {Data.ad_spaces.web_ad_image && <img className="adimage" src={Data.ad_spaces.web_ad_image} alt="ads" />}
                        </div>
                    </div>
                </div>
            ) : null}

            {/* videos */}
            {(Data.videos && Data.videos?.length > 0) ? (
                <div id="rns-main" className="video_style_four">
                    <div className="container">
                        <div className="row">
                                <div id="rns-cards-main" className="">
                                <div id="rns-head-main" className="">
                                        <div className="left-sec">
                                            <h4 id="rns-main-logo" className="mb-0">{Data.title}</h4>
                                            <div className="short_desc">{Data && Data.short_description}</div>
                                        </div>

                                        <Link href="/" id="rns-Viewmore" to={`/video-news-view/${Data.id}`} onClick={() => scrollToTop()}>
                                            {translate("viewMore")}
                                        </Link>
                                    </div>

                                    <div className="row">
                                        {Data.videos.map((value, index) => {
                                            return (
                                                <div className="col-lg-4 col-md-4 col-sm-6 col-12">
                                                    <div id="rns-card" className="card card_hover_two" key={index} onClick={() => { handleVideoUrl(value.content_value); TypeUrl(value.type) }}>
                                                        <div className="banner_thumb">
                                                            <img id="rns-image" src={value.image} className="card-img-top" alt="news" onError={placeholderImage}/>
                                                        </div>
                                                        <div id="rns-img-overlay" className=" card-inverse">
                                                            <Link id="vps-btnVideo" >
                                                                <BsFillPlayFill id="vps-btnVideo-logo" className="pulse" fill="white" size={50} />
                                                            </Link>
                                                        </div>
                                                        <div id="rns-card-body" className="card-block pb-0">
                                                            <p className="card-title mb-0">{value.title}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            );
                                        })}

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
                        </div>
                    </div>
                </div>
            ) : null}

             {/* ad spaces */}
             {Data.ad_spaces && Data.id === Data.ad_spaces.ad_featured_section_id && Data.news_type === "news" ? (
                <div className="ad_spaces">
                    <div className="container">
                    <div target="_blank" onClick={() => window.open(Data.ad_spaces.ad_url, '_blank')}>
                        {Data.ad_spaces.web_ad_image && <img className="adimage" src={Data.ad_spaces.web_ad_image} alt="ads" />}
                    </div>
                    </div>
                </div>
            ) : null}

            {/* news */}
            {(Data && Data.news?.length > 0) ? (
                <div id="rns-main" className="news_style_four">
                    <div className="container">
                        <div className="row">
                                <div id="rns-cards-main" className="">
                                <div id="rns-head-main" className="">
                                        <div className="left-sec">
                                            <h4 id="rns-main-logo" className="mb-0">{Data.title}</h4>
                                            <div className="short_desc">{Data && Data.short_description}</div>
                                        </div>

                                        <Link href="/" id="rns-Viewmore" to={`/view-all/${Data.id}`} onClick={() => scrollToTop()}>
                                            {translate("viewMore")}
                                        </Link>
                                    </div>

                                    <div className="row">
                                        {Data.news.map((value, index) => {
                                            return (
                                                <div className="col-lg-4 col-md-4 col-sm-6 col-12">
                                                    <Link id="rns-card" className="card card_hover_two" key={index} to={`/news/${value.id}/${value.category_id}`}>
                                                        <div className="banner_thumb">
                                                            <img id="rns-image" src={value.image} className="card-img-top" alt="news" onError={placeholderImage}/>
                                                        </div>
                                                        <div id="rns-img-overlay" className=" card-inverse">
                                                            <Link id="btnrnsCatagory" className="btn btn-sm" type="button" >
                                                                {truncateText(value.category_name, 10)}
                                                            </Link>
                                                        </div>
                                                        <div id="rns-card-body" className="card-block">
                                                            <p className="card-title">{value.title}</p>
                                                            <Link id="btnrnsRead" className="btn overlay" to={`/news/${value.id}/${value.category_id}`} type="button">
                                                            {translate("readmore")}
                                                                <HiOutlineArrowLongRight id="rns-arrow" size={20} />
                                                            </Link>
                                                        </div>
                                                    </Link>
                                                </div>
                                            );
                                        })}

                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            ) : null}

             {/* ad spaces */}
             {Data.ad_spaces && Data.id === Data.ad_spaces.ad_featured_section_id && Data.news_type === "breaking_news" ? (
                <div className="ad_spaces">
                    <div className="container">
                    <div target="_blank" onClick={() => window.open(Data.ad_spaces.ad_url, '_blank')}>
                                    {Data.ad_spaces.web_ad_image && <img className="adimage" src={Data.ad_spaces.web_ad_image} alt="ads" />}
                                </div>
                    </div>
                </div>
            ) : null}

            {/* breaking news */}
            {(Data && Data.breaking_news?.length > 0) ? (
                <div id="rns-main">
                    <div className="container">
                        <div className="row">
                                <div id="rns-cards-main" className="">
                                    <div id="rns-head-main" className="">
                                        <div className="left-sec">
                                            <h4 id="rns-main-logo" className="mb-0">{Data.title}</h4>
                                            <div className="short_desc">{Data && Data.short_description}</div>
                                        </div>
                                        <Link href="/" id="rns-Viewmore" to={`/view-all/${Data.id}`} onClick={() => scrollToTop()}>
                                            {translate("viewMore")}
                                        </Link>
                                    </div>

                                    <div className="row">
                                        {Data.breaking_news.map((value, index) => {
                                            return (
                                                <div className="col-lg-4 col-md-4 col-sm-6 col-12">
                                                    <Link id="rns-card" className="card card_hover_two" key={index} to={`/breaking-news/${value.id}`}>
                                                    <div className="banner_thumb">
                                                            <img id="rns-image" src={value.image} className="card-img-top" alt="news" onError={placeholderImage}/>
                                                        </div>
                                                        <div id="rns-card-body" className="card-block">
                                                            <p className="card-title">{value.title}</p>
                                                            <Link id="btnrnsRead" className="btn overlay" to={`/breaking-news/${value.id}`} type="button">
                                                            {translate("readmore")}
                                                                <HiOutlineArrowLongRight id="rns-arrow" size={20} />
                                                            </Link>
                                                        </div>
                                                    </Link>
                                                </div>
                                            );
                                        })}

                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            ): null}
        </>
    );
}

export default StyleFour;
