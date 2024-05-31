import breakingNews2_jpg from "../images/earthImage.png";
import { Link } from "react-router-dom";
import { convertToSlug, placeholderImage, translate, truncateText } from "../utils";
import SwiperCore, { Navigation, Pagination } from "swiper";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/swiper-bundle.css";
import Skeleton from "react-loading-skeleton";
import { BsFillPlayFill } from "react-icons/bs";
import VideoPlayerModal from "./VideoPlayerModal";
import { useState } from "react";
SwiperCore.use([Navigation, Pagination]);
const StyleFive = ({ isLoading, Data }) => {

    const [Video_url, setVideo_url] = useState();
    const [modalShow, setModalShow] = useState(false);
    const [typeUrl, setTypeUrl] = useState(null);

    function handleVideoUrl(url) {
        setModalShow(true);
        setVideo_url(url);
    }

    const showNavigation = Data.news?.length > 1;

    const showNavigationBreaking = Data.breaking_news?.length > 1;

    const showNavigationVideo = Data.videos?.length > 1;

    const swiperOption = {
        loop: true,
        speed: 750,
        spaceBetween: 10,
        slidesPerView: 2,
        navigation: showNavigation,
        breakpoints: {
            0: {
                slidesPerView: 1,
            },

            768: {
                slidesPerView: 2,
            },

            992: {
                slidesPerView: 2,
            },
            1200: {
                slidesPerView: 3,
            },
        },
        autoplay: true,
    };

    const swiperOptionVideo = {
        loop: true,
        speed: 750,
        spaceBetween: 10,
        slidesPerView: 2,
        navigation: showNavigationVideo,
        breakpoints: {
            0: {
                slidesPerView: 1,
            },

            768: {
                slidesPerView: 2,
            },

            992: {
                slidesPerView: 2,
            },
            1200: {
                slidesPerView: 3,
            },
        },
        autoplay: true,
    };

    const swiperOptionBreaking = {
        loop: true,
        speed: 750,
        spaceBetween: 10,
        slidesPerView: 2,
        navigation: showNavigationBreaking,
        breakpoints: {
            0: {
                slidesPerView: 1,
            },

            768: {
                slidesPerView: 2,
            },

            992: {
                slidesPerView: 2,
            },
            1200: {
                slidesPerView: 3,
            },
        },
        autoplay: true,
    };

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

            {/* videos */}
            {(Data.videos && Data.videos?.length > 0) ? (
                <div id="bns-main" className="video_style_five">
                    <div className="container custom-card">
                        <div className="row">
                            <div className="col-md-4 col-12">
                                <div id="bns-main-card" className="card">
                                    <img id="bns-main-image" src={breakingNews2_jpg} className="card-img" alt="news" onError={placeholderImage}/>
                                    <div id="bns-main-text" className="card-img-overlay">
                                        <p id="bns-logo-col" className="card-text">
                                            <b>
                                                {Data.title}
                                            </b>
                                        </p>
                                        <p id="bns-logo-row" className="card-text">
                                            <b>{Data.title}</b>
                                        </p>
                                        <Link id="btnbnsViewAll" className="btn" type="button" to={`/video-news-view/${Data.id}`} onClick={() => scrollToTop()}>
                                            {translate("viewall")}
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-8 col-12">
                                <div id="bns-rest-cards">
                                    <Swiper {...swiperOptionVideo} >
                                        {isLoading ? (
                                            // Show skeleton loading when data is being fetched
                                            <div className="col-12 loading_data">
                                                <Skeleton height={20} count={22} />
                                            </div>
                                        ) : (
                                            Data.videos.map((element) => (
                                                <SwiperSlide key={element.id}>
                                                    <div id="bns-card" className="card" key={element.id}>
                                                        <Link id="Link-all" onClick={() => {handleVideoUrl(element.content_value); TypeUrl(element.type)}}>
                                                            <img id="bns-image" src={element.image} className="card-img-top" alt="news" onError={placeholderImage}/>
                                                            <div id="rns-img-overlay" className=" card-inverse">
                                                                <Link id="vps-btnVideo" >
                                                                    <BsFillPlayFill id="vps-btnVideo-logo" className="pulse" fill="white" size={50} />
                                                                </Link>
                                                            </div>
                                                            <div id="bns-card-body" className="card-body ps-0">
                                                                <h5 id="bns-card-text" className="">
                                                                    {element.title}
                                                                </h5>
                                                            </div>
                                                        </Link>
                                                    </div>
                                                </SwiperSlide>
                                            ))
                                        )}
                                    </Swiper>
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
                <div id="bns-main" className="news_style_five">
                    <div className="container custom-card">
                        <div className="row">
                            <div className="col-md-4 col-12">
                                <div id="bns-main-card" className="card">
                                    <img id="bns-main-image" src={breakingNews2_jpg} className="card-img" alt="news" onError={placeholderImage}/>
                                    <div id="bns-main-text" className="card-img-overlay">
                                        <p id="bns-logo-col" className="card-text">
                                            <b>
                                                {Data.title}
                                            </b>
                                        </p>
                                        <p id="bns-logo-row" className="card-text">
                                            <b>{Data.title}</b>
                                        </p>
                                        <Link id="btnbnsViewAll" className="btn" type="button" to={`/view-all/${Data.id}`} onClick={() => scrollToTop()}>
                                            {translate("viewall")}
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-8 col-12">
                                <div id="bns-rest-cards">
                                    <Swiper {...swiperOption} >
                                        {isLoading ? (
                                            // Show skeleton loading when data is being fetched
                                            <div className="col-12 loading_data">
                                                <Skeleton height={20} count={22} />
                                            </div>
                                        ) : (
                                            Data.news.map((element) => (
                                                <SwiperSlide key={element.id}>
                                                    <div id="bns-card" className="card" key={element.id}>
                                                        <Link id="Link-all" to={`/news/${element.id}/${element.category_id}`}>
                                                            <img id="bns-image" src={element.image} className="card-img-top" alt="news" onError={placeholderImage}/>
                                                            <div id="bns-card-body" className="card-body ps-0">
                                                                <Link id="btnbnsCatagory" className="btn btn-sm" type="button" to={`/news/${element.id}/${element.category_id}`}>
                                                                    {truncateText(element.category_name, 10)}
                                                                </Link>
                                                                <h5 id="bns-card-text" className="">
                                                                    {element.title}
                                                                </h5>
                                                            </div>
                                                        </Link>
                                                    </div>
                                                </SwiperSlide>
                                            ))
                                        )}
                                    </Swiper>
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
                <div id="bns-main">
                    <div className="container custom-card">
                        <div className="row">
                            <div className="col-md-4 col-12">
                                <div id="bns-main-card" className="card">
                                    <img id="bns-main-image" src={breakingNews2_jpg} className="card-img" alt="news" onError={placeholderImage}/>
                                    <div id="bns-main-text" className="card-img-overlay">
                                        <p id="bns-logo-col" className="card-text">
                                            <b>
                                                {Data.title}
                                            </b>
                                        </p>
                                        <p id="bns-logo-row" className="card-text">
                                            <b>{Data.title}</b>
                                        </p>
                                        <Link id="btnbnsViewAll" className="btn" type="button" to={`/view-all/${Data.id}`} onClick={() => scrollToTop()}>
                                            {translate("viewall")}
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-8 col-12">
                                <div id="bns-rest-cards">
                                    <Swiper {...swiperOptionBreaking} >
                                        {isLoading ? (
                                            // Show skeleton loading when data is being fetched
                                            <div className="col-12 loading_data">
                                                <Skeleton height={20} count={22} />
                                            </div>
                                        ) : (
                                            Data.breaking_news.map((element) => (
                                                <SwiperSlide key={element.id}>
                                                    <div id="bns-card" className="card" key={element.id}>
                                                        <Link id="Link-all" to={`/breaking-news/${element.id}`}>
                                                            <img id="bns-image" src={element.image} className="card-img-top" alt="news" onError={placeholderImage}/>
                                                            <div id="bns-card-body" className="card-body ps-0">
                                                                <h5 id="bns-card-text" className="">
                                                                    {element.title}
                                                                </h5>
                                                            </div>
                                                        </Link>
                                                    </div>
                                                </SwiperSlide>
                                            ))
                                        )}
                                    </Swiper>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ) : null}
        </>
    )

}

export default StyleFive;
