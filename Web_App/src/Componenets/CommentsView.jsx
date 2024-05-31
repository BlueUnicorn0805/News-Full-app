import { useState, useEffect, useRef } from "react";
import Button from "react-bootstrap/Button";
import OverlayTrigger from "react-bootstrap/OverlayTrigger";
import Popover from "react-bootstrap/Popover";
import { deletecommentApi, getcommentbynewsApi, setCommentLikeDislikeApi, setFlagApi, setcommentApi } from "../store/actions/campaign";
import { useSelector } from "react-redux";
import { selectUser } from "../store/reducers/userReducer";
import { translate } from "../utils";
import no_image from "../images/images.jpeg";
import { Modal } from "antd";
import { BiDotsVerticalRounded, BiSolidDislike, BiSolidFlag, BiSolidLike, BiSolidTrash } from "react-icons/bi";
import { toast } from "react-toastify";

function CommentsView(props) {
    const [LoadComments, setLoadComments] = useState(false);
    const [refreshKey, setRefreshKey] = useState(0);
    const [Data, setData] = useState([]);
    const [Comment, setComment] = useState("");
    const Nid = props.Nid;
    const replyRef = useRef();
    const userData = useSelector(selectUser);
    const [modalOpen, setModalOpen] = useState(false);
    const [dotModal,setDotModal] = useState(false)
    const [CommentID,setCommentID] = useState(null)
    const [message,setMessage] = useState(null)

    useEffect(() => {
        getcommentbynewsApi(
            Nid,
            "0",
            "10",
            (response) => {
                if (response.data === undefined) {
                    setData([]);
                } else {
                    setData(response.data);
                }
            },
            (error) => {
                console.log(error);
            }
        );
    }, [Nid, props.LoadComments, LoadComments,refreshKey]);

    // set comment
    const setCommentData = (e, id) => {
        e.preventDefault();
        setcommentApi(
            id,
            Nid,
            Comment,
            (response) => {
                setLoadComments(true);
                setTimeout(() => {
                    setLoadComments(false);
                }, 1000);
            },
            (error) => {
                console.log(error);
            }
        );
    };

    // set replay comment
    const setreplyComment = (e, id) => {
        e.preventDefault();
        setcommentApi(
            id,
            Nid,
            Comment,
            (response) => {
                setLoadComments(true);
                setTimeout(() => {
                    setLoadComments(false);
                }, 1000);
            },
            (error) => {
                console.log(error);
            }
        );
    };

    // like button
    const LikeButton = (e,elem) => {
        e.preventDefault();
            setCommentLikeDislikeApi(elem.id,elem.like === "1" ? "0" : "1",(res)=>{
                // console.log(res);
                setRefreshKey((prevKey) => prevKey + 1);
                
            },(err)=>{
                console.log(err);
            })
        
       
    }

    // dislike
    const dislikebutton = (e,elem) => {
        e.preventDefault();
        setCommentLikeDislikeApi(elem.id,elem.dislike === "1" ? "0" : "2",(res)=>{
            // console.log(res);
            setRefreshKey((prevKey) => prevKey + 1);
        },(err)=>{
            console.log(err);
        })
    }

    // dots
    const popupDots = (e,elem) =>{
        console.log(elem)
        e.preventDefault();
        setModalOpen(true)
        // console.log("popupDots",elem.user_id)
        if(userData.data.id === elem.user_id){
            setDotModal(true)
        }else{
            setDotModal(false)
        }
    }

    const deleteComment = (e) => {
        e.preventDefault();
        deletecommentApi(CommentID,(res)=>{
            setLoadComments(true);
            setRefreshKey((prevKey) => prevKey + 1);
            setModalOpen(false)
            toast.success(translate("comDelSucc"))

        },(err)=>{
            console.log(err)
        })
    }

    const submitBtn = (e) => {
        e.preventDefault();
        setFlagApi(CommentID,Nid,message,(res)=>{
            setRefreshKey((prevKey) => prevKey + 1);
            setModalOpen(false)
            setLoadComments(true);
            setMessage("")
            toast.success(translate("flag"))
        },(err)=>{
            console.log(err);
        })
    }


    return (
        <>
        {userData && userData.data ? (
        <div>
            {Data.length === 0 ? null : <h2>{translate("comment")}</h2>}
            {Data &&
                Data.map((element) => (
                    <div key={element.id}>
                        <div id="cv-comment" onClick={() => setCommentID(element.id)}>
                        <img id="cs-profile" src={element.profile ? element.profile : no_image} alt="" />
                            <div id="cs-card" className="card">
                                <b>
                                    <h5>{element.name}</h5>
                                </b>
                                {/* <Link id="cdbtnReport">Report</Link> */}
                                <p id="cs-card-text" className="card-text">
                                    {element.message}
                                </p>
                                {["bottom-end"].map((placement) =>
                                 
                                        <>
                                        <div className="comment_data">
                                            {/* {console.log("place", element)} */}
                                            <div className="comment_like">
                                                <BiSolidLike size={22} onClick={(e)=> LikeButton(e,element)}/>{element.like > 0 ? element.like : null}
                                            </div>
                                            <div className="comment_dislike">
                                                <BiSolidDislike size={22} onClick={(e) => dislikebutton(e,element)}/>{element.dislike > 0 ? element.dislike : null}
                                            </div>
                                            <div className="comment_dots">
                                                <BiDotsVerticalRounded size={22} onClick={(e) => popupDots(e,element)}/>
                                            </div>
                                        </div>
                                        <OverlayTrigger
                                            trigger="click"
                                            key={placement}
                                            placement={placement}
                                            rootClose
                                            overlay={
                                                <Popover id={`popover-positioned-${placement}`}>
                                                    <Popover.Header as="h3">{translate("addreplyhere")}</Popover.Header>
                                                    <Popover.Body id="cv-replay-propover">
                                                        <form id="cv-replay-form" method="post" onSubmit={(e) => setCommentData(e,element.id)}>
                                                            <textarea
                                                                name=""
                                                                id="cs-reply-input"
                                                                cols="30"
                                                                rows="5"
                                                                onChange={(e) => {
                                                                    setComment(e.target.value);
                                                                }}
                                                                placeholder="Share Your reply..."
                                                            ></textarea>
                                                            <button id="cdbtnsubReplay" type="submit" className="btn">
                                                            {translate("submitreply")}
                                                            </button>
                                                        </form>
                                                    </Popover.Body>
                                                </Popover>
                                            }
                                        >
                                            <Button id={`${element.id}`} className="cdbtnReplay" variant="secondary" ref={replyRef}>
                                            {translate("reply")}
                                            </Button>
                                        </OverlayTrigger>
                                        </>
                                  
                                )}
                            </div>
                        </div>
                        {element.replay.map((ele) => (
                            <div id="cv-Rcomment" key={ele.id} onClick={() => setCommentID(ele.id)}>
                                <img id="cs-profile" src={ele.profile ? ele.profile : no_image} alt="" />
                                <div id="cs-Rcard" className="card">
                                    <b>
                                        <h5>{ele.name}</h5>
                                    </b>
                                    {/* <Link id="cdbtnReport">Report</Link> */}
                                    <p id="cs-card-text" className="card-text">
                                        {ele.message}
                                    </p>
                                    {["bottom-end"].map((placement) =>
                                       
                                            <>
                                            <div className="comment_data">
                                            {/* {console.log("place", element)} */}
                                            <div className="comment_like">
                                                <BiSolidLike size={22} onClick={(e)=> LikeButton(e,ele)}/>{ele.like > 0 ? ele.like : null}
                                            </div>
                                            <div className="comment_dislike">
                                                <BiSolidDislike size={22} onClick={(e) => dislikebutton(e,ele)}/>{ele.dislike > 0 ? ele.dislike : null}
                                            </div>
                                            <div className="comment_dots">
                                                <BiDotsVerticalRounded size={22} onClick={(e) => popupDots(e,ele)}/>
                                            </div>
                                        </div>
                                           
                                            <OverlayTrigger
                                                trigger="click"
                                                key={placement}
                                                placement={placement}
                                                rootClose
                                                overlay={
                                                    <Popover id={`popover-positioned-${placement}`}>
                                                        <Popover.Header as="h3">{translate("addreplyhere")}</Popover.Header>
                                                        <Popover.Body id="cv-replay-propover">
                                                            <form method="post" onSubmit={(e) => setreplyComment(e,ele.parent_id)}>
                                                                <textarea
                                                                    name=""
                                                                    id="cs-input"
                                                                    cols="30"
                                                                    rows="5"
                                                                    onChange={(e) => {
                                                                        setComment(e.target.value);
                                                                    }}
                                                                    placeholder="Share Your reply..."
                                                                ></textarea>
                                                                <button id="cdbtnsubReplay" type="submit" className="btn">
                                                                {translate("submitreply")}
                                                                </button>
                                                            </form>
                                                        </Popover.Body>
                                                    </Popover>
                                                }
                                            >
                                                <Button id={`${element.id}`} className="cdbtnReplay" variant="secondary" ref={replyRef}>
                                                {translate("reply")}
                                                </Button>
                                            </OverlayTrigger>
                                            </>
                                       
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                ))}
                <Modal centered className="comment_Modal" open={modalOpen} maskClosable={true} onOk={() => setModalOpen(false)} onCancel={() => setModalOpen(false)} footer={false} >
                   {dotModal ? 
                   <div className="comment_delete" onClick={(e)=> deleteComment(e)}>
                        <p className="mb-0">{translate("deleteTxt")}</p><BiSolidTrash size={18}/>
                   </div>
                   : 
                   <div className="comment_report">
                    <div className="comment_title">
                        <p className="mb-0">{translate("reportTxt")}</p><BiSolidFlag size={18}/>
                    </div>
                        <textarea value={message} name="" id="" cols="30" rows="5" onChange={(e) => setMessage(e.target.value)}/>
                        <div className="comment_bottom">
                            <button type="submit" className="btn btn-secondary" onClick={(e)=> submitBtn(e)}>{translate("submitBtn")}</button>
                        </div>
                   </div>
                    }
                 </Modal>
        </div>
    ): null}
    </>
    )
}

export default CommentsView;
