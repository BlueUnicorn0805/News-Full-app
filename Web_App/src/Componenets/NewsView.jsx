import { useState, useEffect } from "react";
import Form from "react-bootstrap/Form";
import { AiOutlineLike, AiTwotoneLike,AiOutlineEye } from "react-icons/ai";
import { BsBookmark, BsFillBookmarkFill, BsFillPlayFill } from "react-icons/bs";
import { FiCalendar } from "react-icons/fi";
import React from "react";
import RelatedNewsSection from "./RelatedNewsSection";
import { Link, useNavigate, useParams } from "react-router-dom";
import TagsSection from "./TagsSection";
import CommentSection from "./CommentSection";
import BreadcrumbNav from "./BreadcrumbNav";
import { FacebookIcon, WhatsappIcon, TwitterIcon, TwitterShareButton, WhatsappShareButton, FacebookShareButton } from "react-share";
import SignInModal from "./SignInModal";
import { getadsspacenewsdetailsApi, getnewsbyApi, setbookmarkApi, setlikedislikeApi, setnewsviewApi } from "../store/actions/campaign";
import { getUser } from "../utils/api";
import { calculateReadTime, convertToSlug, extractTextFromHTML, isLogin, translate } from "../utils";
import VideoPlayerModal from "./VideoPlayerModal";
import { selectCurrentLanguage } from "../store/reducers/languageReducer";
import { useSelector } from "react-redux";
import Skeleton from "react-loading-skeleton";
import { selectUser } from "../store/reducers/userReducer";
import { settingsData } from "../store/reducers/settingsReducer";
import { GoTag } from "react-icons/go";
import { BiTime } from "react-icons/bi";

// import { useLocation } from 'react-router-dom';

const NewsView = () => {
    const [Data, setData] = useState([]); // eslint-disable-next-line
    let user = getUser();
    const currentLanguage = useSelector(selectCurrentLanguage);
    const userData = useSelector(selectUser);
    const settingsOnOff = useSelector(settingsData);
    const navigate = useNavigate();
    // eslint-disable-next-line
    const [CheckLike, setCheckLike] = useState(false);
    const [Like, setLike] = useState(CheckLike); // eslint-disable-next-line
    const [Bookmark, setBookmark] = useState(false); // eslint-disable-next-line
    const [FontSize, setFontSize] = useState(14); // eslint-disable-next-line
    const [Video_url, setVideo_url] = useState();
    const [sponsoredads, setSponsoredAds] = useState(null);
    const [modalShow, setModalShow] = useState(false);
    const [VideomodalShow, setVideoModalShow] = useState(false);
    const [loading, setLoading] = useState(true);
    const [typeUrl,setTypeUrl] = useState(null);
    const { newsid, catid } = useParams();
    const NewsId = newsid;
    const CategoryID = catid;

    console.log("data",NewsId,CategoryID)


    const shareUrl = window.location.href;
    // eslint-disable-next-line
    const [islogout, setIsLogout] = useState(false); // eslint-disable-next-line
    const [isloginloading, setisloginloading] = useState(true); // eslint-disable-next-line

    useEffect(() => {
        getnewsbyApi(
            NewsId,
            currentLanguage.id,
            (response) => {
                setData(response.data);
                setLoading(false)
                if (response.data[0].bookmark === "0") {
                    setBookmark(false);
                } else {
                    setBookmark(true);
                }

                if (response.data[0].like === "0") {
                    setLike(false);
                } else {
                    setLike(true);
                }
            },
            (error) => {
                if (error === "No Data Found") {
                    setData("");
                    setLoading(false);
                }

            }
        );
    }, [NewsId,currentLanguage]);

    // set like dislike
    const setLikeDislikeData = (id, status) => {
        if (user !== null) {
            setlikedislikeApi(
                id,
                status,
                (response) => {
                    setLike(!Like);
                },
                (error) => {
                    console.log(error);
                }
            );
        } else {
            setModalShow(true);
        }
    };

    // set bookmark
    const setbookmarkData = (newsid, status) => {
        if (user !== null) {
            setbookmarkApi(
                newsid,
                status,
                (response) => {
                    setBookmark(!Bookmark);
                },
                (error) => {
                    console.log(error);
                }
            );
        } else {
            setModalShow(true);
        }
    };

    function handleVideoUrl(url) {
        setVideoModalShow(true);
        setVideo_url(url);
    }

    useEffect(() => {
    }, [userData.data]);


    useEffect(() => {
        setnewsviewApi(NewsId, (response) => {
           
        }, (error) => {
            console.log(error);
        })
    }, [NewsId])

    const TypeUrl = (type) => {
        setTypeUrl(type)
    }

    useEffect(() => {
        window.scrollTo(0, 0);
    }, []);

    useEffect(() => {
        getadsspacenewsdetailsApi((response) => {
            setSponsoredAds(response.data)
        }, (error) => {
            console.log("res", error)
        });
    }, []);

    // tags
    const tagSplit = (tag) => {
        let tags = tag.split(",");
        return tags;
    }

    // const readTime = calculateReadTime(text);

    const text = extractTextFromHTML(Data && Data[0]?.description);

    // Calculate read time
    const readTime = calculateReadTime(text);



    return (
        <>
            {loading ? (
                 <div>
                    <Skeleton height={200} count={3} />
                </div>
            ) : Data && Data.length > 0 ? (
                <>
                    <BreadcrumbNav SecondElement="News Details" ThirdElement={Data && Data[0].title} />
                    <div className="news-deatail-section">
                            <div id="nv-main" className="container news_detail">
                                 {/* ad spaces */}
                                 {sponsoredads && sponsoredads.ad_spaces_top ? (
                                    <div className="ad_spaces">
                                        <div target="_blank" onClick={() => window.open(sponsoredads && sponsoredads.ad_spaces_top.ad_url, '_blank')}>
                                            {<img className="adimage" src={sponsoredads && sponsoredads.ad_spaces_top.web_ad_image} alt="ads" />}
                                        </div>
                                    </div>
                                ) : null}
                            <div id="nv-page" className="row">
                                <div id="nv-body" className="col-lg-8 col-12">
                                    <button id="btnnvCatagory" className="btn btn-sm" type="button">
                                        {Data && Data[0].category_name}
                                    </button>
                                    <h1 id="nv-title">{Data && Data[0].title}</h1>

                                    <div id="nv-Header" className="">
                                        <div id="nv-left-head">
                                            <p id="head-lables">
                                                <FiCalendar size={18} id="head-logos" /> {Data && Data[0].date.slice(0, 10)}
                                            </p>
                                            <p id="head-lables">
                                                <AiOutlineLike size={18} id="head-logos" /> {Data && Data[0].total_like} {translate("likes")}
                                                </p>

                                                <p id="head-lables" className="eye_icon">
                                                    <AiOutlineEye size={18} id="head-logos"/> {Data && Data[0].total_views}
                                                </p>
                                                <p id="head-lables" className="minute_Read">
                                                
                                                    <BiTime size={18} id="head-logos"/>
                                                    {
                                                       readTime && readTime > 1 ? (
                                                            " " + readTime + " " + translate("minutes") + " " + translate("read")
                                                        ) : (
                                                            " " + readTime + " " + translate("minute") + " " + translate("read")
                                                        )
                                                    }
                                                </p>
                                        </div>

                                        <div id="nv-right-head">
                                            <h6 id="nv-Share-Label">{translate("shareLbl")}:</h6>
                                            <FacebookShareButton url={shareUrl} title={Data && Data[0].title + " - News"} hashtag={"News"}>
                                                <FacebookIcon size={40} round />
                                            </FacebookShareButton>
                                            <WhatsappShareButton url={shareUrl} title={Data && Data[0].title + " - News"} hashtag={"News"}>
                                                <WhatsappIcon size={40} round />
                                            </WhatsappShareButton>
                                            <TwitterShareButton url={shareUrl} title={Data && Data[0].title + " - News"} hashtag={"News"}>
                                                <TwitterIcon size={40} round />
                                            </TwitterShareButton>
                                        </div>
                                    </div>
                                    <div id="vps-body-left">
                                        <img id="nv-image" src={Data && Data[0].image} alt="..." />
                                        {Data && Data[0].content_value ? (
                                            <div className="text-black">
                                                <Link id="vps-btnVideo" onClick={() => {handleVideoUrl(Data && Data[0].content_value); TypeUrl(Data && Data[0].type)}}>
                                                    <BsFillPlayFill id="vps-btnVideo-logo" fill="white" size={50} />
                                                </Link>
                                            </div>
                                        ) : null}
                                    </div>
                                    {/* <CarouselSection images={Data[0].image}/> */}

                                    <nav id="nv-functions" className="custom-font">
                                        <div id="nv-functions-left" className="col-md-10 col-12">
                                            <Form.Label id="nv-font-lable">{translate("fontsize")}</Form.Label>
                                            <Form.Range id="nv-FontRange" min={14} max={24} step={2} value={FontSize} onChange={(e) => setFontSize(e.target.value)} />
                                            <div className="d-flex justify-content-between">
                                                <Form.Label id="nv-FontRange-labels">14px</Form.Label>
                                                <Form.Label id="nv-FontRange-labels">16px</Form.Label>
                                                <Form.Label id="nv-FontRange-labels">18px</Form.Label>
                                                <Form.Label id="nv-FontRange-labels">20px</Form.Label>
                                                <Form.Label id="nv-FontRange-labels">22px</Form.Label>
                                                <Form.Label id="nv-FontRange-labels">24px</Form.Label>
                                            </div>
                                            {/* <h1>{FontSize}</h1> */}
                                        </div>
                                        {isLogin() ?
                                            <div id="nv-functions-right" className="col-md-2 col-12">
                                                <div id="nv-function-pair">
                                                    <button id="nv-function" className="btn" onClick={() => setbookmarkData(Data && Data[0].id, !Bookmark ? 1 : 0)}>
                                                        {Bookmark ? <BsFillBookmarkFill size={23} /> : <BsBookmark size={23} />}
                                                    </button>
                                                    <p id="nv-function-text">{translate("saveLbl")}</p>
                                                </div>
                                                    <div id="nv-function-pair">

                                                            <button id="nv-function" className="btn" onClick={() => setLikeDislikeData(NewsId, !Like ? 1 : 0)}>
                                                                {Like ? <AiTwotoneLike size={23} /> : <AiOutlineLike size={23} />}
                                                            </button>

                                                    <p id="nv-function-text">{translate("likes")}</p>
                                                </div>
                                            </div>
                                            :
                                        null}
                                    </nav>
                                    <p id="nv-description" style={{ fontSize: `${FontSize}px` }} dangerouslySetInnerHTML={{ __html: Data && Data[0].description }}></p>

                                        {/* tags */}
                                        {Data[0].tag_name ? (
                                            <div className="tags_section_outer">
                                                <div className="inner_tag">
                                                    <div className="tag_icon">
                                                        <GoTag/>
                                                    </div>
                                                    <div className="tag_name">
                                                        {translate("tagLbl")} :
                                                    </div>
                                                    <div className="tag_data">
                                                        <span className="tags_section">
                                                            {tagSplit(Data[0].tag_name).map((tag, index) => (
                                                            <p
                                                                    key={index}
                                                                    className="mb-0 me-2 new-view-tags"
                                                                    onClick={() => navigate(`/tag/${Data[0].tag_id}`)}
                                                            >
                                                                {tag}
                                                            </p>
                                                            ))}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        ) : null}

                                        {/* // <p id='nv-description' dangerouslySetInnerHTML={{__html: Data[0].description}}></p> */}
                                        {settingsOnOff && settingsOnOff.comments_mode === "1" ?
                                            <CommentSection Nid={NewsId} />
                                            : <>
                                                <div className="text-center my-5">{translate("comDisable")}</div>
                                            </>}
                                </div>

                                    <div id="nv-right-section" className="col-lg-4 col-12">
                                        {CategoryID ?
                                            <RelatedNewsSection Cid={CategoryID} Nid={NewsId}/> : null
                                    }
                                    <TagsSection />
                                </div>
                            </div>
                            <VideoPlayerModal
                                show={VideomodalShow}
                                onHide={() => setVideoModalShow(false)}
                                // backdrop="static"
                                keyboard={false}
                                    url={Video_url}
                                    type_url={typeUrl}
                                // title={Data[0].title}
                            />
                            <SignInModal setIsLogout={setIsLogout} setisloginloading={setisloginloading} show={modalShow} setLoginModalShow={setModalShow} onHide={() => setModalShow(false)} />
                                {/* ad spaces */}
                                {sponsoredads && sponsoredads.ad_spaces_bottom ? (
                                    <div className="ad_spaces my-3">
                                        <div target="_blank" onClick={() => window.open(sponsoredads && sponsoredads.ad_spaces_bottom.ad_url, '_blank')}>
                                            {<img className="adimage" src={sponsoredads && sponsoredads.ad_spaces_bottom.web_ad_image} alt="ads" />}
                                        </div>
                                    </div>
                                ) : null}
                            </div>
                    </div>

                </>
                ) : (
                    <div className="text-center my-5">{translate("nodatafound")}</div>
            )}
        </>
    );
}

export default NewsView;
