import { BsFillPlayFill, BsPlayCircle } from "react-icons/bs";
import { Link } from "react-router-dom";
import { Breathing } from "react-shimmer";
import SwiperCore, { Navigation, Pagination,Autoplay } from "swiper";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/swiper-bundle.css";
import Skeleton from "react-loading-skeleton";
import { convertToSlug, placeholderImage, translate, truncateText } from "../utils";
import { useState } from "react";
import VideoPlayerModal from "./VideoPlayerModal";

SwiperCore.use([Navigation, Pagination,Autoplay]);

const StyleOne = ({ isLoading, Data }) => {

    const truncate = (input) => (input?.length > 180 ? `${input.substring(0, 180)}...` : input);

    const swiperOption = {
        loop: false,
        speed: 750,
        spaceBetween: 10,
        slidesPerView: 1,
        navigation: false,
        autoplay:{
            delay: 2000000,
            disableOnInteraction: false,
        },
        pagination: { clickable: true },
    };

    const [Video_url, setVideo_url] = useState();
    const [modalShow, setModalShow] = useState(false);
    const [typeUrl,setTypeUrl] = useState(null);


    function handleVideoUrl(url) {
        setModalShow(true);
        setVideo_url(url);
    }

    const TypeUrl = (type) => {
        setTypeUrl(type)
    };

    return (
        <div id="first-section">

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

            {/* video section */}
            {(Data.videos && Data.videos?.length > 0) ? (
                <Swiper {...swiperOption} className="custom-swiper">
                    {isLoading ? (
                        // Show skeleton loading when data is being fetched
                        <div className="col-12 loading_data">
                            <Skeleton height={20} count={22} />
                        </div>
                    ) : (
                        Data.videos.slice(0,3).map((item) => (
                            <SwiperSlide key={item.id}>
                                <div id="fs-main" className="h-100 video_style_one inner_custom_swiper">
                                    <div id="body-first-section" className="container">
                                        <div className="row" onClick={() => {handleVideoUrl(item.content_value); TypeUrl(item.type)}}>
                                            <div className="col-xl-7 order-1 order-xl-0 col-12 d-flex">
                                                <div id="Left-first-section" className="my-auto">
                                                    <div id="Top-Description" className="my-3" dangerouslySetInnerHTML={{ __html: truncate(item.title) }}></div>
                                                </div>
                                            </div>
                                            <div className="col-xl-5 order-0 order-xl-1 col-12">
                                                <div id="right-first-section">
                                                    <img src={item.image} className="float-end fs-Newscard-image h-auto" id="fs-Newscard-image" fallback={<Breathing width={800} height={600} />} alt="news" onError={placeholderImage} />
                                                    <div className="circle">
                                                        <BsFillPlayFill id="btnpaly-logo" className="pulse" fill="white" size={50} />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </SwiperSlide>
                        ))
                    )}
                    <VideoPlayerModal
                        show={modalShow}
                        onHide={() => setModalShow(false)}
                        // backdrop="static"
                        keyboard={false}
                        url={Video_url}
                        type_url={typeUrl}
                        // title={Data[0].title}
                    />
                </Swiper>

            )
                : null}

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

            {/* news section */}
            {(Data && Data.news?.length > 0) ? (
                <Swiper {...swiperOption} className="custom-swiper">
                    {isLoading ? (
                        // Show skeleton loading when data is being fetched
                        <div className="col-12 loading_data">
                            <Skeleton height={20} count={22} />
                        </div>
                    ) : (
                        Data.news.slice(0,3).map((item) => (
                            <SwiperSlide key={item.id}>
                                <div id="fs-main" className="h-100 inner_custom_swiper news_style_one">
                                    <div id="body-first-section" className="container">
                                        <div className="row">
                                            <div className="col-xl-7 order-1 order-xl-0 col-12 d-flex">
                                                <div id="Left-first-section" className="my-auto">
                                                    <Link id="btnCatagory" className="btn" type="button" to={`/news/${item.id}/${item.category_id}`}>
                                                        {truncateText(item.category_name, 10)}
                                                    </Link>
                                                    <div className="my-3 top-title">{truncateText(item.title, 60)}</div>
                                                    {/* <p className="mb-3 para" dangerouslySetInnerHTML={{ __html: item && truncateText(item.description,200) }}></p> */}
                                                    <div>
                                                        <Link id="btnReadMore" className="btn mb-0" type="button" to={`/news/${item.id}/${item.category_id}`}>
                                                            <b>{translate("readmore")}</b>
                                                        </Link>
                                                        {item.content_value ?
                                                            <Link id="btnpaly" onClick={() => handleVideoUrl(item.content_value)} className="circle">
                                                                <BsPlayCircle id="btnpaly-logo" size={40} />
                                                            </Link>
                                                        : null}
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="col-xl-5 order-0 order-xl-1 col-12">
                                                <div id="right-first-section">
                                                    <img src={item.image} className="float-end fs-Newscard-image h-auto" id="fs-Newscard-image" fallback={<Breathing width={800} height={600} />} alt="news" onError={placeholderImage}/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </SwiperSlide>
                        ))
                    )}
                    <VideoPlayerModal
                        show={modalShow}
                        onHide={() => setModalShow(false)}
                        // backdrop="static"
                        keyboard={false}
                        url={Video_url}
                        // title={Data[0].title}
                    />
                </Swiper>
            )
                : null}

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

            {/* breaking news section */}
            {(Data && Data.breaking_news?.length > 0) ? (
                <Swiper {...swiperOption} className="custom-swiper">
                    {isLoading ? (
                        // Show skeleton loading when data is being fetched
                        <div className="col-12 loading_data">
                            <Skeleton height={20} count={22} />
                        </div>
                    ) : (
                        Data.breaking_news.slice(0,3).map((item) => (
                            <SwiperSlide key={item.id}>
                                <div id="fs-main" className="h-100 inner_custom_swiper">
                                    <div id="body-first-section" className="container">
                                        <div className="row">
                                            <div className="col-xl-7 order-1 order-xl-0 col-12 d-flex">
                                                <div id="Left-first-section" className="my-auto">
                                                    <div id="Top-Description" className="my-3" >{item.title }</div>
                                                    {/* <p className="mb-3 para" dangerouslySetInnerHTML={{ __html: item && item.description }}></p> */}
                                                    <div>
                                                        <Link id="btnReadMore" className="btn mb-0" type="button" to={`/breaking-news/${item.id}`}>
                                                            <b>{translate("readmore")}</b>
                                                        </Link>
                                                        {item.content_value ?
                                                            <Link id="btnpaly" onClick={() => handleVideoUrl(item.content_value)} className="circle">
                                                                <BsPlayCircle id="btnpaly-logo" size={40} />
                                                            </Link>
                                                        : null}
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="col-xl-5 order-0 order-xl-1 col-12">
                                                <div id="right-first-section">
                                                    <img src={item.image} className="float-end fs-Newscard-image h-auto" id="fs-Newscard-image" fallback={<Breathing width={800} height={600} />} alt="news" onError={placeholderImage}/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </SwiperSlide>
                        ))
                    )}
                    <VideoPlayerModal
                        show={modalShow}
                        onHide={() => setModalShow(false)}
                        // backdrop="static"
                        keyboard={false}
                        url={Video_url}
                        // title={Data[0].title}
                    />
                </Swiper>
            )
                : null}

        </div>
    );
}

export default StyleOne;
