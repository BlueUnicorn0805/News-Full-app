import React, { useState } from "react";
import CommentsView from "./CommentsView";
import { setcommentApi } from "../store/actions/campaign";
import {isLogin, translate} from "../utils"
import { toast } from "react-toastify";

function CommentSection(props) {
    const [Comment, setComment] = useState("");
    const [LoadComments, setLoadComments] = useState(false);
    const Nid = props.Nid;
    const setCommentData = (e) => {
        e.preventDefault();
        if (!isLogin()) {
            toast.error("please login first to comment")
            return
        }
        setcommentApi(
            "0",
            Nid,
            Comment,
            (response) => {
                setLoadComments(true);
                setComment("")
                setTimeout(() => {
                    setLoadComments(false);
                }, 1000);
            },
            (error) => {
                console.log(error);
            }
        );
    };

    return (
        <div>
            <form id="cs-main" onSubmit={(e) => setCommentData(e)}>
                <h2>{translate("leaveacomments")}</h2>
                <textarea
                    value={Comment}
                    name=""
                    id="cs-input"
                    cols="30"
                    rows="10"
                    onChange={(e) => {
                        setComment(e.target.value);
                    }}
                    placeholder={`${translate("shareThoghtLbl")}`}
                ></textarea>
                <button id="cs-btnsub" type="submit" className="btn">
                {translate("submitpost")}
                </button>
            </form>
            <CommentsView Nid={Nid} LoadComments={LoadComments} />
        </div>
    );
}

export default CommentSection;
