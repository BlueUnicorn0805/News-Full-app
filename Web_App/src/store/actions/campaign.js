import { deleteCommentApi, getBookmarkApi, getBreakingNewsApi, getBreakingNewsIdApi, getCategoriesApi, getCommentByNewsApi, getLiveStreamingApi, getNewsApi, getNewsByCategoryApi, getNewsByIdApi, getPagesApi, getTagApi, getVideoApi, setBookmarkApi, setCommentApi, setLikeDisLikeApi, getNewsByTagApi, DeleteUserNotificationApi, getFeatureSectionApi, getFeatureSectionByIdApi, setUserCategoriesApi, getUserCategoriesApi, getUserByIdApi, setNewsViewApi, setBreakingNewsViewApi, getAdsSpaceNewsDetailsApi, setnewsApi, deleteimageApi, deletenewsApi, getsubcategorybycategoryApi, set_comment_like_dislike_Api, set_flag_Api } from "../../utils/api"
import { store } from "../store"
import { apiCallBegan } from "./apiActions"

// 1. get categories
export const categoriesApi = (offset, limit,language_id, onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getCategoriesApi(offset, limit,language_id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 2. get breaking news
export const getbreakingNewsApi = (onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getBreakingNewsApi(),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 3. get news
export const getnewsApi = (offset, limit, get_user_news, search,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getNewsApi(offset, limit, get_user_news, search),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 4. get video
export const getvideoApi = (onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getVideoApi(),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 5. get news by category
export const getnewsbycategoryApi = (category_id,subcategory_id,offset,limit,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getNewsByCategoryApi(category_id,subcategory_id,offset,limit),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 6. get breaking news by id
export const getbreakingnewsidApi = (breaking_news_id,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getBreakingNewsIdApi(breaking_news_id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 7. get tags
export const gettagsApi = (onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getTagApi(),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 8. get pages
export const getpagesApi = (onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getPagesApi(),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 9. get live streaming
export const getlivestreamApi = (onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getLiveStreamingApi(),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 9. get bookmark
export const getbookmarkApi = (offset,limit,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getBookmarkApi(offset,limit),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 10. set bookmark
export const setbookmarkApi = (news_id,status,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...setBookmarkApi(news_id,status),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 11. set comment
export const setcommentApi = (parent_id,news_id,message,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...setCommentApi(parent_id,news_id,message),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

//12. get comment
export const getcommentbynewsApi = (news_id,offset,limit,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getCommentByNewsApi(news_id,offset,limit),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

//13. delete comment
export const deletecommentApi = (comment_id,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...deleteCommentApi(comment_id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 14. get news by id
export const getnewsbyApi = (news_id,language_id,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getNewsByIdApi(news_id,language_id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 15. set likedislike
export const setlikedislikeApi = (news_id,status,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...setLikeDisLikeApi(news_id,status),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 16. get news by tag
export const getnewsbytagApi = (tag_id,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getNewsByTagApi(tag_id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 17. delete user notification
export const deleteusernotificationApi = (id,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...DeleteUserNotificationApi(id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 18. get feature sections
export const getfeaturesectionApi = (onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getFeatureSectionApi(),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 19. get feature section by id
export const getfeaturesectionbyidApi = (section_id,offset,limit,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getFeatureSectionByIdApi(section_id,offset,limit),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 20. set user categories
export const setusercategoriesApi = (category_id,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...setUserCategoriesApi(category_id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 21. get user categories
export const getusercategoriesApi = (category_id,offset,limit,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getUserCategoriesApi(category_id,offset,limit),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 22. get user by id
export const getuserbyidApi = (onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getUserByIdApi(),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 23. set news view
export const setnewsviewApi = (news_id,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...setNewsViewApi(news_id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 24. set breaking news view
export const setbreakingnewsviewApi = (breaking_news_id,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...setBreakingNewsViewApi(breaking_news_id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 25. get ad space news details
export const getadsspacenewsdetailsApi = (onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getAdsSpaceNewsDetailsApi(),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 26. set news
export const setNewsApi = (action_type,category_id,subcategory_id,tag_id,title,content_type,content_data,description,image,ofile,show_till,language_id,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...setnewsApi(action_type,category_id,subcategory_id,tag_id,title,content_type,content_data,description,image,ofile,show_till,language_id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 27. delete new images
export const deleteImageApi = (image_id,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...deleteimageApi(image_id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 28. delete news
export const deleteNewsApi = (news_id,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...deletenewsApi(news_id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 29. subcategory by category
export const getSubcategoryByCategoryApi = (category_id,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getsubcategorybycategoryApi(category_id),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 30. set comment like dislike
export const setCommentLikeDislikeApi = (comment_id,status,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...set_comment_like_dislike_Api(comment_id,status),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};

// 31. set flag
export const setFlagApi = (comment_id,news_id,message,onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...set_flag_Api(comment_id,news_id,message),
        displayToast: false,
        onStart,
        onSuccess,
        onError,
    }));
};