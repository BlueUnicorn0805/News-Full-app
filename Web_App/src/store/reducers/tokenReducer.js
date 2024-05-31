import { createSelector, createSlice } from "@reduxjs/toolkit";
import { store } from "../store";
import { apiCallBegan } from "../actions/apiActions";
import { generateTokenApi } from "../../utils/api";

const initialState = {
    token: null,
    loading: false,
}

export const tokenSlice = createSlice({
    name: "token",
    initialState,
    reducers: {
        tokenSuccess: (token, action) => {
            token.token = action.payload.data
            token.loading = false;
            // token.data = action.payload.data
        },
        tokenRemoveSuccess: (token) => {
            token = initialState;
            return token;
        },
    }
})


export const { tokenSuccess,tokenRemoveSuccess } = tokenSlice.actions;
export default tokenSlice.reducer;

// token api call
export const tokenApi = (onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...generateTokenApi(),
        displayToast: false,
        onStart,
        onSuccess,
        onError
    }))
};

// load api
export const loadToken = (data) => {
    store.dispatch(tokenSuccess({data}));
};



// Selector Functions
export const tokenData = createSelector(
    state => state.token,
    token => token.token,
)
