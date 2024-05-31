import { createSelector, createSlice } from "@reduxjs/toolkit";
import { store } from "../store";
import { apiCallBegan } from "../actions/apiActions";
import { getSettingsApi } from "../../utils/api";

const initialState = {
    data: null,
    loading: false,
}

export const settingsSlice = createSlice({
    name: "settings",
    initialState,
    reducers: {
        settingsRequested: (settings, action) => {
            settings.loading = true;
        },
        settingsSuccess: (settings, action) => {
            settings.data = action.payload.data
            settings.loading = false;
            // token.data = action.payload.data
        },
        settingsFailed: (websettings, action) => {
            websettings.loading = false;
        },

    }
})


export const { settingsRequested,settingsSuccess,settingsFailed } = settingsSlice.actions;
export default settingsSlice.reducer;

// load websettings api call
export const laodSettingsApi = (onSuccess, onError, onStart) => {
    store.dispatch(apiCallBegan({
        ...getSettingsApi(),
        displayToast: false,
        onStartDispatch: settingsRequested.type,
        onSuccessDispatch: settingsSuccess.type,
        onErrorDispatch: settingsFailed.type,
        onStart,
        onSuccess,
        onError
    }))
};


// Selector Functions
export const settingsData = createSelector(
    state => state.settings,
    settings => settings.data,
)
