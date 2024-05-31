import { createSelector, createSlice } from "@reduxjs/toolkit";
import { store } from "../store";
import { apiCallBegan } from "../actions/apiActions";
import { getWebSettingsApi } from "../../utils/api";

const initialState = {
    data: null,
    loading: false,
}

export const websettingsSlice = createSlice({
    name: "websettings",
    initialState,
    reducers: {
        websettingsRequested: (websettings, action) => {
            websettings.loading = true;
        },
        websettingsSuccess: (websettings, action) => {
            websettings.data = action.payload.data
            websettings.loading = false;
            // token.data = action.payload.data
        },
        websettingsFailed: (websettings, action) => {
            websettings.loading = false;
        },

    }
})


export const { websettingsRequested,websettingsSuccess,websettingsFailed } = websettingsSlice.actions;
export default websettingsSlice.reducer;

// load websettings api call
export const laodwebsettingsApi = (onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getWebSettingsApi(),
        displayToast: false,
        onStartDispatch: websettingsRequested.type,
        onSuccessDispatch: websettingsSuccess.type,
        onErrorDispatch: websettingsFailed.type,
        onStart,
        onSuccess,
        onError
    }))
};


// Selector Functions
export const webSettingsData = createSelector(
    state => state.websettings,
    websettings => websettings.data,
)
