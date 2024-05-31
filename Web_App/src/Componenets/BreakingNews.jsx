import { useState, useEffect } from "react";
import Form from "react-bootstrap/Form";
import { Link, useParams } from "react-router-dom";
import TagsSection from "./TagsSection";
import { FacebookIcon, WhatsappIcon, TwitterIcon, TwitterShareButton, WhatsappShareButton, FacebookShareButton } from "react-share";
import RelatedBreakingNews from "./RelatedBreakingNews";
import BreadcrumbNav from "./BreadcrumbNav";
import { getadsspacenewsdetailsApi, getbreakingnewsidApi, setbreakingnewsviewApi } from "../store/actions/campaign";
import { useSelector } from "react-redux";
import { selectCurrentLanguage } from "../store/reducers/languageReducer";
import { calculateReadTime, extractTextFromHTML, translate } from "../utils";
import { BsFillPlayFill } from "react-icons/bs";
import { AiOutlineEye } from "react-icons/ai";
import VideoPlayerModal from "./VideoPlayerModal";
import Skeleton from "react-loading-skeleton";
import { BiTime } from "react-icons/bi";

const BreakingNews = () => {
    const [Data, setData] = useState([]);
    const [FontSize, setFontSize] = useState(14);
    const [Video_url, setVideo_url] = useState();
    const [modalShow, setModalShow] = useState(false);
    const [isLoading, setIsLoading] = useState(true);
    const [sponsoredads, setSponsoredAds] = useState(null);
    const { bnewsid } = useParams();
    const BNid = bnewsid;
    const shareUrl = window.location.href;
    const currentLanguage = useSelector(selectCurrentLanguage);

    useEffect(() => {
        getbreakingnewsidApi(
            BNid,
            (response) => {
                setData(response.data);
                setIsLoading(false);
            },
            (error) => {
                setIsLoading(false);
                if (error === "No Data Found") {
                  setData("");
                }
            }
        );
    }, [BNid, currentLanguage]);

    function handleVideoUrl(url) {
        setModalShow(true);
        setVideo_url(url);
    }

    useEffect(() => {
        setbreakingnewsviewApi(BNid, (response) => {
            
        }, (error) => {
            console.log(error);
        })
    }, [BNid])

    useEffect(() => {
        window.scrollTo(0, 0);
    },[])


    useEffect(() => {
        getadsspacenewsdetailsApi((response) => {
            setSponsoredAds(response.data)
        }, (error) => {
            console.log("res", error)
        });
    }, []);

    const text = extractTextFromHTML(Data && Data[0]?.description);

    // Calculate read time
    const readTime = calculateReadTime(text);


    return (
        <>
            {Data && Data?.length > 0 ? (
                <>
                    {isLoading ? (
                        // Show skeleton loading when data is being fetched
                        <div className="col-12 loading_data">
                            <Skeleton height={20} count={22} />
                        </div>

                    ) : (
                        <>
                                <BreadcrumbNav SecondElement={translate("breakingNewsLbl")} ThirdElement={Data[0].title} />

                                <div className="breaking-news-section">
                                    <div id="B_NV-main" className="breaking_news_detail">
                                        <div id="B_NV-page" className="container">
                                             {/* ad spaces */}
                                            {sponsoredads && sponsoredads.ad_spaces_top ? (
                                                <div className="ad_spaces mb-5">
                                                    <div target="_blank" onClick={() => window.open(sponsoredads && sponsoredads.ad_spaces_top.ad_url, '_blank')}>
                                                        {<img className="adimage" src={sponsoredads && sponsoredads.ad_spaces_top.web_ad_image} alt="ads" />}
                                                    </div>
                                                </div>
                                            ) : null}
                                        <div className="row">
                                            <div className="col-md-7 col-12">
                                                <div id="B_NV-body">
                                                    <p id="btnB_NVCatagory" className="btn btn-sm mb-0">
                                                        {translate("breakingnews")}
                                                    </p>
                                                    <h1 id="B_NV-title">{Data[0].title}</h1>

                                                    <div id="B_NV-Header" className="">
                                                    <div id="nv-left-head">
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

                                                        <div id="B_NV-right-head">
                                                            <h6 id="B_NV-Share-Label">{translate("shareLbl")}:</h6>
                                                            <FacebookShareButton url={shareUrl} title={Data[0].title + " - News"} hashtag={"News"}>
                                                                <FacebookIcon size={30} round />
                                                            </FacebookShareButton>
                                                            <WhatsappShareButton url={shareUrl} title={Data[0].title + " - News"} hashtag={"News"}>
                                                                <WhatsappIcon size={30} round />
                                                            </WhatsappShareButton>
                                                            <TwitterShareButton url={shareUrl} title={Data[0].title + " - News"} hashtag={"News"}>
                                                                <TwitterIcon size={30} round />
                                                            </TwitterShareButton>
                                                            <Link></Link>
                                                        </div>
                                                    </div>
                                                    <div id="vps-body-left">
                                                        <img id="B_NV-image" src={Data[0].image} alt="..." />
                                                        {Data[0] && Data[0].content_value ? (
                                                            <div className="text-black">
                                                                <Link id="vps-btnVideo" onClick={() => handleVideoUrl(Data[0].content_value)}>
                                                                    <BsFillPlayFill id="vps-btnVideo-logo" className="pulse" fill="white" size={50} />
                                                                </Link>
                                                            </div>
                                                        ) : null}
                                                    </div>

                                                    <nav id="B_NV-functions" className="">
                                                        <div id="B_NV-functions-left">
                                                            <Form.Label id="B_NV-font-lable">{translate("fontsize")}</Form.Label>
                                                            <Form.Range id="B_NV-FontRange" min={14} max={24} step={2} value={FontSize} onChange={(e) => setFontSize(e.target.value)} />
                                                            <div className="d-flex justify-content-between">
                                                                <Form.Label id="B_NV-FontRange-labels">14px</Form.Label>
                                                                <Form.Label id="B_NV-FontRange-labels">16px</Form.Label>
                                                                <Form.Label id="B_NV-FontRange-labels">18px</Form.Label>
                                                                <Form.Label id="B_NV-FontRange-labels">20px</Form.Label>
                                                                <Form.Label id="B_NV-FontRange-labels">22px</Form.Label>
                                                                <Form.Label id="B_NV-FontRange-labels">24px</Form.Label>
                                                            </div>
                                                        </div>
                                                        <div id="B_NV-functions-right"></div>
                                                    </nav>
                                                    <p id="B_NV-description" style={{ fontSize: `${FontSize}px` }} dangerouslySetInnerHTML={{ __html: Data[0].description }}></p>
                                                </div>
                                            </div>
                                            <div className="col-md-5 col-12">
                                                <div id="B_NV-right-section">
                                                        {BNid ? <RelatedBreakingNews id={BNid} /> : null}
                                                    <TagsSection />
                                                </div>
                                            </div>
                                        </div>
                                        <VideoPlayerModal
                                            show={modalShow}
                                            onHide={() => setModalShow(false)}
                                            // backdrop="static"
                                            keyboard={false}
                                                url={Video_url}
                                                type_url={Data[0].type}
                                        // title={Data[0].title}
                                            />
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
                                </div>
                        </>
                    )
                    }
                </>
            ): null}
        </>
    );
}

export default BreakingNews;
