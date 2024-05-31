importScripts('https://www.gstatic.com/firebasejs/9.15.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/9.15.0/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
const firebaseConfig = {
    apiKey: "AIzaSyC6-DZ3rX5H7RqzOdlq4alYsRKsUkU_F6I",
    authDomain: "news-9b899.firebaseapp.com",
    projectId: "news-9b899",
    storageBucket: "news-9b899.appspot.com",
    messagingSenderId: "1085975883181",
    appId: "1:1085975883181:web:7084701e5bd77855704524",
    measurementId: "G-5WV6VE3XFK"
};
// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
firebase.initializeApp(firebaseConfig);
let messaging = null;
if (firebase.messaging.isSupported()){
    messaging = firebase.messaging();
}
// const messaging = firebase.messaging();
messaging.onBackgroundMessage((payload) => {
    console.log(
        '[firebase-messaging-sw.js] Received background message ',
        payload
    );
    // Customize notification here
    const notificationTitle = 'Background Message Title';
    const notificationOptions = {
        body: 'Background Message body.',
        icon: '/firebase-logo.png'
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
});