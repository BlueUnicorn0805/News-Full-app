import { createAction } from "@reduxjs/toolkit";
/**
 *
 * @params
 * url :
 * method : GET / POST / PUT / DELETE
 * data : object
 * onStart : Redux action creator
 * onSuccess : Redux action creator
 * onError : Redux action creator
 * headers : object
 * displayToast : true / false, default : true
 * authorizationHeader : true / false , default : true --> if Authorisation Header should be set in request or not
 */
export const apiCallBegan = createAction("api/CallBegan");
export const apiCallSuccess = createAction("api/CallSuccess");
export const apiCallFailed = createAction("api/CallFailed");