import { createSelector, createSlice } from "@reduxjs/toolkit";
import { apiCallBegan } from "../actions/apiActions";
import { getLanguageJsonDataApi, getLanguagesApi } from "../../utils/api";
import { store } from "../store";


const initialState = {
    list: [],
    loading: false,
    lastFetch: null,
    currentLanguage: {
        id:null,
        code: null,
        name: null
    },
    currentLanguageLabels: {
        loading: true,
        data: {},
        lastFetch: null,
    },
};


export const languageSlice = createSlice({
    name: 'languages',
    initialState,
    reducers: {
        languagesRequested: (languages, action) => {
            languages.loading = true;
        },
        languagesReceived: (languages, action) => {
            languages.list = action.payload.data;
            languages.loading = false;
            languages.lastFetch = Date.now();
        },
        languagesRequestFailed: (languages, action) => {
            languages.loading = false;
        },
        languageChanged: (languages, action) => {
            languages.currentLanguage.code = action.payload.code;
            languages.currentLanguage.name = action.payload.name;
            languages.currentLanguage.id = action.payload.id;
        },
        languageLabelRequested: (languages, action) => {
            languages.currentLanguageLabels.loading = true;
        },
        languageLabelsReceived: (languages, action) => {
            languages.currentLanguageLabels.data = action.payload.data;
            languages.currentLanguageLabels.loading = false;
            // languages.currentLanguageLabels.lastFetch = Date.now();
        },
        languageLabelRequestFailed: (languages, action) => {
            languages.currentLanguageLabels.loading = true;
        },
    }
});

export const {languagesRequested, languagesReceived, languagesRequestFailed,languageChanged,languageLabelRequested,languageLabelsReceived,languageLabelRequestFailed} = languageSlice.actions;
export default languageSlice.reducer;


// API Calls
export const loadLanguages = (onSuccess, onError, onStart) => {
    // const {lastFetch} = store.getState().Languages;
    // const diffInMinutes = moment().diff(moment(lastFetch), 'minutes');
    // // If API data is fetched within last 10 minutes then don't call the API again
    // if (diffInMinutes < 10) return false;
    store.dispatch(apiCallBegan({
        ...getLanguagesApi(),
        displayToast: false,
        onStartDispatch: languagesRequested.type,
        onSuccessDispatch: languagesReceived.type,
        onErrorDispatch: languagesRequestFailed.type,
        onStart,
        onSuccess,
        onError
    }))
};

export const loadLanguageLabels = (code, onSuccess, onError, onStart) => {

    store.dispatch(apiCallBegan({
        ...getLanguageJsonDataApi(code),
        displayToast: false,
        onStartDispatch: languageLabelRequested.type,
        onSuccessDispatch: languageLabelsReceived.type,
        onErrorDispatch: languageLabelRequestFailed.type,
        onStart,
        onSuccess,
        onError
    }))
};

export const setCurrentLanguage = (name, code,id) => {
    store.dispatch(languageChanged({name, code,id}));
};

// Selector Functions
export const selectLanguages = createSelector(
    state => state.languages,
    languages => languages.list,
)

export const selectCurrentLanguage = createSelector(
    state => state.languages.currentLanguage,
    languages => languages,
);

export const selectCurrentLanguageLabels = createSelector(
    state => state.languages.currentLanguageLabels.data,
    languages => languages
)