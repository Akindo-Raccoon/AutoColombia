// ============================================================
// ARCHIVO: public/firebase-messaging-sw.js
// FUNCIÓN: Service Worker para recibir notificaciones push
//          cuando el navegador está en segundo plano.
// IMPORTANTE: Este archivo DEBE estar en la raíz del proyecto
//             o en /public/ para que Firebase lo encuentre.
// ============================================================

// Importar el script de Firebase para el Service Worker
importScripts('https://www.gstatic.com/firebasejs/12.13.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.13.0/firebase-messaging-compat.js');

// Configuración de Firebase (igual que en firebase-config.js)
firebase.initializeApp({
    apiKey:            "AIzaSyA4y5dRPlSi0ksU8kLiq1-BMCqNMmEQPWE",
    authDomain:        "autocolombia-7106d.firebaseapp.com",
    projectId:         "autocolombia-7106d",
    storageBucket:     "autocolombia-7106d.firebasestorage.app",
    messagingSenderId: "628269753538",
    appId:             "1:628269753538:web:3a437dfcc18dfc5bd0475b"
});

// Obtener instancia de Messaging
const messaging = firebase.messaging();

// Manejar mensajes en background (cuando el tab no está activo)
messaging.onBackgroundMessage(function(payload) {
    console.log('[SW] Mensaje en background recibido:', payload);

    const titulo  = payload.notification.title || 'AutoColombia';
    const opciones = {
        body: payload.notification.body || 'Tienes una nueva notificación',
        icon: '/public/images/logo.png'  // pon aquí tu logo si tienes
    };

    // Mostrar la notificación del sistema operativo
    self.registration.showNotification(titulo, opciones);
});
