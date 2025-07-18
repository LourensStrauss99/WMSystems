importScripts('https://www.gstatic.com/firebasejs/10.11.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.11.0/firebase-messaging-compat.js');

const firebaseConfig = {
  apiKey: "AIzaSyB7S2G-bfOCeILZlA-2DBFKBJakAXr3WpY",
  authDomain: "wmsystems-2af66.firebaseapp.com",
  projectId: "wmsystems-2af66",
  storageBucket: "wmsystems-2af66.firebasestorage.app",
  messagingSenderId: "560740833259",
  appId: "1:560740833259:web:22a3cb1365482e1daa2bae",
  measurementId: "G-1GWTS43TF3",
  vapidKey: "BD-wo4IqQMZX8NVuloW_c-gWLVKPrpyljtVeRTmkk07fZgEvcXwXglqNkyS7u4ulvxxEvFLM74HAlf4Otlgxm3Q"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  self.registration.showNotification(
    payload.notification.title,
    {
      body: payload.notification.body,
      icon: '/acme_logo.png',
    }
  );
});
