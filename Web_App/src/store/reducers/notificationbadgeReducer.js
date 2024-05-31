import { createSlice } from "@reduxjs/toolkit";
import { store } from "../store";
import { apiCallBegan } from "../actions/apiActions";
import { getNotificationsApi, getUserNotificationApi } from "../../utils/api";

// state
const initialState = {
    counter: 0,
};

// slice
export const counterSlice = createSlice({
    name: 'counter',
    initialState,
    reducers: {
        counterSuccess: (counter, action) => {
            const { data } = action.payload;
            counter.counter = data.length;
        },
        counterFailure: (counter, action) => {
            if (action.payload === "No Data Found") {
                counter.counter = 0;
            }
        }
    }

});

export const { counterSuccess,counterFailure } = counterSlice.actions;
export default counterSlice.reducer;

// api calls

// personal user notification
export const loaduserNotification = (offset,limit, onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getUserNotificationApi(offset,limit),
        displayToast: false,
        onSuccessDispatch: counterSuccess.type,
        onErrorDispatch: counterFailure.type,
        onStart,
        onSuccess,
        onError
    }))
};

// notification
export const loadNotification = (offset,limit, onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getNotificationsApi(offset,limit),
        displayToast: false,
        onSuccessDispatch: counterSuccess.type,
        onStart,
        onSuccess,
        onError
    }))
};


// selectors
export const counterData = (state) => state.counter;
