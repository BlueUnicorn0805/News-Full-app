import { createSelector, createSlice } from "@reduxjs/toolkit";
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
    createToEdit: null,
};


export const createNewsSlice = createSlice({
    name: 'createNews',
    initialState,
    reducers: {
        languageChanged: (createNews, action) => {
            createNews.currentLanguage.code = action.payload.code;
            createNews.currentLanguage.name = action.payload.name;
            createNews.currentLanguage.id = action.payload.id;
        },
        managetoEditData: (createNews,action) => {
            createNews.createToEdit = action.payload.data
        },

    }
});

export const {languageChanged,managetoEditData} = createNewsSlice.actions;
export default createNewsSlice.reducer;


export const setCreateNewsCurrentLanguage = (name, code, id) => {
    store.dispatch(languageChanged({name, code,id}));
};

export const loadManageToEdit = (data) => {
    store.dispatch(managetoEditData({data}));
}


// Selector Functions

export const selectcreateNewsCurrentLanguage = createSelector(
    state => state.createNews.currentLanguage,
    createNews => createNews,
);

export const selectManageNews = createSelector(
    state => state.createNews.createToEdit,
    createNews => createNews,
);
