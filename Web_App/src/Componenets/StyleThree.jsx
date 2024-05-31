import React, { useState } from "react";
import { BsFillPlayFill } from "react-icons/bs";
import Card from "react-bootstrap/Card";
import { Link } from "react-router-dom";
import VideoPlayerModal from "./VideoPlayerModal";
import { convertToSlug, placeholderImage, translate, truncateText } from "../utils"



const StyleThree = ({ Data }) => {

     const [Video_url, setVideo_url] = useState();
    const [modalShow, setModalShow] = useState(false);
    const [typeUrl,setTypeUrl] = useState(null);


    function handleVideoUrl(url) {
        setModalShow(true);
        setVideo_url(url);
    }

    const scrollToTop = () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    };

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

            {/* video */}
            {(Data.videos && Data.videos?.length > 0) ? (
                <div id="vps-main" className="video_style_three">
                    <div className="container">
                        <div className="row">
                            <div id="vps-head-main" className="">
                                <div className="left-sec">
                                    <p id="vps-main-logo" className="mb-0">{Data && Data.title}</p>
                                    <div className="short_desc">{Data && Data.short_description}</div>
                                </div>
                                <Link href="/" id="vps-Viewmore" onClick={() => scrollToTop()} to={`/video-news-view/${Data.id}`}>
                                    {translate("viewMore")}
                                </Link>
                            </div>
                        </div>
                        <div className="row">
                            <div className="col-lg-6 col-12">
                                <div id="vps-body-left">
                                    {Data.videos[0] ? (
                                        <div className="div" onClick={() => { handleVideoUrl(Data.videos[0].content_value); TypeUrl(Data.videos[0].type) }}>
                                            <Card id="vps-main-card" className="text-black" >
                                                <Card.Img id="vps-main-image" src={Data.videos[0].image} alt="news" onError={placeholderImage}/>

                                                <Card.ImgOverlay>
                                                    <Link id="vps-btnVideo" >
                                                        <BsFillPlayFill id="vps-btnVideo-logo" className="pulse" fill="white" size={50} />
                                                    </Link>
                                                </Card.ImgOverlay>
                                            </Card>
                                            <p id="vps-card-title">
                                                <b>{Data.videos[0].title}</b>
                                            </p>
                                        </div>
                                    ) : null}
                                </div>
                            </div>
                            <div className="col-lg-6 col-12">
                                <div id="vps-body-right">
                                    {Data.videos[1] ? (
                                        <Card id="vps-image-cards" className="text-black second_video" onClick={() => {handleVideoUrl(Data.videos[1].content_value); TypeUrl(Data.videos[1].type)}}>
                                            <Card.Img id="vps-secondry-images" src={Data.videos[1].image} alt="news" onError={placeholderImage}/>
                                            <Card.ImgOverlay>
                                                <Link id="vps-btnVideo" >
                                                    <BsFillPlayFill id="vps-btnVideo-logo" className="pulse" fill="white" size={50} />
                                                </Link>
                                            </Card.ImgOverlay>
                                                <p id="vps-card-title">
                                                    <b>{Data.videos[1].title}</b>
                                                </p>
                                        </Card>
                                    ) : null}

                                    {Data.videos[2] ? (
                                        <Card id="vps-image-cards" className="text-black third_video" onClick={() => {handleVideoUrl(Data.videos[2].content_value); TypeUrl(Data.videos[2].type)}}>
                                            <Card.Img id="vps-secondry-images" src={Data.videos[2].image} alt="news" onError={placeholderImage}/>
                                            <Card.ImgOverlay>
                                                <Link id="vps-btnVideo" >
                                                    <BsFillPlayFill id="vps-btnVideo-logo" className="pulse" fill="white" size={50} />
                                                </Link>
                                            </Card.ImgOverlay>
                                                <p id="vps-card-title">
                                                    <b>{Data.videos[2].title}</b>
                                                </p>
                                        </Card>
                                    ) : null}
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
                <div id="vps-main" className="news_style_three">
                    <div className="container">
                        <div className="row">
                            <div id="vps-head-main" className="">
                                <div className="left-sec">
                                    <p id="vps-main-logo" className="mb-0">{Data && Data.title}</p>
                                    <div className="short_desc">{Data && Data.short_description}</div>
                                </div>
                                <Link href="/" id="vps-Viewmore" onClick={() => scrollToTop()} to={`/view-all/${Data.id}`}>
                                    {translate("viewMore")}
                                </Link>
                            </div>
                        </div>
                        <div className="row">
                            <div className="col-lg-6 col-12">
                                <div id="vps-body-left">
                                    {Data.news[0] ? (
                                        <Link to={`/news/${Data.news[0].id}/${Data.news[0].category_id}`}>
                                            <Card id="vps-main-card" className="text-black">
                                                <Card.Img id="vps-main-image" src={Data.news[0].image} alt="news" onError={placeholderImage} />
                                            </Card>
                                            <Link className="style_three_cat_name">
                                                {truncateText(Data.news[0].category_name, 10)}
                                            </Link>
                                            <p id="vps-card-title" className="mt-2">
                                                <b>{Data.news[0].title}</b>
                                            </p>
                                        </Link>
                                    ) : null}
                                </div>
                            </div>
                            <div className="col-lg-6 col-12">
                                <div id="vps-body-right">
                                    {Data.news[1] ? (
                                        <Link to={`/news/${Data.news[1].id}/${Data.news[1].category_id}`}>
                                            <Card id="vps-image-cards" className="text-black second_video">
                                                <Card.Img id="vps-secondry-images" src={Data.news[1].image} alt="news" onError={placeholderImage}/>
                                                <Card.ImgOverlay>
                                                    <div className="inner_Card_content">
                                                        <Link className="style_three_cat_name" >
                                                            {truncateText(Data.news[1].category_name, 10)}
                                                        </Link>
                                                        <p id="vps-card-title">
                                                            <b>{Data.news[1].title}</b>
                                                        </p>
                                                    </div>
                                                </Card.ImgOverlay>
                                            </Card>
                                        </Link>
                                    ) : null}

                                    {Data.news[2] ? (
                                         <Link to={`/news/${Data.news[2].id}/${Data.news[2].category_id}`}>
                                            <Card id="vps-image-cards" className="text-black third_video">
                                                <Card.Img id="vps-secondry-images" src={Data.news[2].image} alt="news" onError={placeholderImage}/>
                                                <Card.ImgOverlay>
                                                    <div className="inner_Card_content">
                                                        <Link className="style_three_cat_name">
                                                            {truncateText(Data.news[2].category_name, 10)}
                                                        </Link>
                                                        <p id="vps-card-title">
                                                            <b>{Data.news[2].title}</b>
                                                        </p>
                                                    </div>
                                                </Card.ImgOverlay>
                                            </Card>
                                        </Link>
                                    ) : null}
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
                <div id="vps-main" className="breaking_news_style_three">
                    <div className="container">
                        <div className="row">
                            <div id="vps-head-main" className="">
                                <div className="left-sec">
                                    <p id="vps-main-logo" className="mb-0">{Data && Data.title}</p>
                                    <div className="short_desc">{Data && Data.short_description}</div>
                                </div>
                                <Link href="/" id="vps-Viewmore" onClick={() => scrollToTop()} to={`/view-all/${Data.id}`}>
                                    {translate("viewMore")}
                                </Link>
                            </div>
                        </div>
                        <div className="row">
                            <div className="col-lg-6 col-12">
                                <div id="vps-body-left">
                                    {Data.breaking_news[0] ? (
                                        <Link to={`/breaking-news/${Data.breaking_news[0].id}`}>
                                            <Card id="vps-main-card" className="text-black">
                                                <Card.Img id="vps-main-image" src={Data.breaking_news[0].image} alt="news" onError={placeholderImage} />
                                            </Card>
                                            <p id="vps-card-title">
                                                <b>{Data.breaking_news[0].title}</b>
                                            </p>
                                        </Link>
                                    ) : null}
                                </div>
                            </div>
                            <div className="col-lg-6 col-12">
                                <div id="vps-body-right">
                                    {Data.breaking_news[1] ? (
                                        <Link to={`/breaking-news/${Data.breaking_news[1].id}`}>
                                            <Card id="vps-image-cards" className="text-black second_video">
                                                <Card.Img id="vps-secondry-images" src={Data.breaking_news[1].image} alt="news" onError={placeholderImage}/>
                                                <Card.ImgOverlay>
                                                    <p id="vps-card-title">
                                                        <b>{Data.breaking_news[1].title}</b>
                                                    </p>
                                                </Card.ImgOverlay>
                                            </Card>
                                        </Link>
                                    ) : null}

                                    {Data.breaking_news[2] ? (
                                        <Link to={`/breaking-news/${Data.breaking_news[2].id}`}>
                                            <Card id="vps-image-cards" className="text-black third_video">
                                                <Card.Img id="vps-secondry-images" src={Data.breaking_news[2].image} alt="news" onError={placeholderImage}/>
                                                <Card.ImgOverlay>
                                                    <p id="vps-card-title">
                                                        <b>{Data.breaking_news[2].title}</b>
                                                    </p>
                                                </Card.ImgOverlay>
                                            </Card>
                                        </Link>
                                    ) : null}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ): null}
        </>
    )

}

export default StyleThree;
