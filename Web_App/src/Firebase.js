import { initializeApp } from "firebase/app";
import {getAuth} from "firebase/auth";
import firebase from "firebase/compat/app"
import {getMessaging,onMessage} from "firebase/messaging";


const firebaseConfig = {
  apiKey: "xxxxxxxx",
  authDomain: "xxxxxxxx",
  projectId: "xxxxxxxx",
  storageBucket: "xxxxxxxx",
  messagingSenderId: "xxxxxxxx",
  appId: "xxxxxxxx",
  measurementId: "xxxxxxxx"
};


    // eslint-disable-next-line
    if (!firebase.apps.length) {
      firebase.initializeApp(firebaseConfig);
  } else {
      firebase.app(); // if already initialized, use that one
  }

  const app = initializeApp(firebaseConfig);
  const messaging = getMessaging(app)
  // onMessage(messaging, (payload) => {
  //   console.log("payload", payload)

  // });

const authentication = getAuth();


export {app, authentication,messaging};
