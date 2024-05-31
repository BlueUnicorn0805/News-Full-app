import { combineReducers, configureStore } from '@reduxjs/toolkit';
import {persistStore, persistReducer} from 'redux-persist'
import storage from 'redux-persist/lib/storage' // defaults to localStorage for web
import tokenReducer from "./reducers/tokenReducer";
import languageReducer from './reducers/languageReducer';
import api from "../store/middleware/api";
import userReducer from './reducers/userReducer';
import websettingsReducer from './reducers/websettingsReducer';
import notificationbadgeReducer from './reducers/notificationbadgeReducer';
import settingsReducer from './reducers/settingsReducer';
import clickActionReducer from "./stateSlice/clickActionSlice";
import createNewsReducer from './reducers/createNewsReducer';

const persistConfig = {
    key: 'root',
    storage,
}

const rootReducer = combineReducers({
    token: tokenReducer,
    languages: languageReducer,
    user: userReducer,
    websettings: websettingsReducer,
    counter: notificationbadgeReducer,
    settings: settingsReducer,
    clickAction: clickActionReducer,
    createNews: createNewsReducer,
});

export const store = configureStore({
    reducer: persistReducer(persistConfig, rootReducer),
    middleware: [
        api
    ]
});

export const persistor = persistStore(store);