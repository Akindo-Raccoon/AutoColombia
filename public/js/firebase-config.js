// Importar los módulos de Firebase desde CDN (versión modular)
import { initializeApp } from "https://www.gstatic.com/firebasejs/12.13.0/firebase-app.js";
import {
    getAuth, 
    signInWithEmailAndPassword,
    createUserWithEmailAndPassword,
    sendPasswordResetEmail,
    signOut
} from "https://www.gstatic.com/firebasejs/12.13.0/firebase-auth.js";

import {
    getMessaging, 
    getToken,
    onMessage
} from "https://www.gstatic.com/firebasejs/12.13.0/firebase-messaging.js";

import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.13.0/firebase-analytics.js";

// Configuración de Firebase
const firebaseConfig = {
    apiKey: "AIzaSyA4y5dRPlSi0ksU8kLiq1-BMCqNMmEQPWE",
    authDomain: "autocolombia-7106d.firebaseapp.com",
    projectId: "autocolombia-7106d",
    storageBucket: "autocolombia-7106d.firebasestorage.app",
    messagingSenderId: "628269753538",
    appId: "1:628269753538:web:3a437dfcc18dfc5bd0475b",
    measurementId: "G-YR8ZLKBQYC"
};

// Inicializar Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const auth = getAuth(app);

// Iniciar sesión. Retorna el usuario autenticado
export async function loginFirebase(email, password) {
    return await signInWithEmailAndPassword(auth, email, password);
}

// Registrar usuario en Firebase 
export async function registrarFirebase(email, password) {
    return await createUserWithEmailAndPassword(auth, email, password);
}

// Recuperar contraseña por email 
export async function recuperarPassword(email) {
    return await sendPasswordResetEmail(auth, email);
}

// Cerrar sesión en Firebase 
export async function cerrarSesionFirebase() {
    return await signOut(auth);
}

// ── FUNCIÓN: Pedir permiso para notificaciones (FCM) ─────────
// VAPID key: la encuentras en Firebase Console → Project Settings → Cloud Messaging
export async function pedirPermisoNotificaciones() {
    try {
        const messaging = getMessaging(app);
        const permission = await Notification.requestPermission();

        if (permission === "granted") {
            // Reemplaza YOUR_VAPID_KEY con tu clave VAPID de Firebase Console
            const token = await getToken(messaging, {
                vapidKey: "628269753538"
            });
            console.log("Token FCM:", token);
            return token;
        } else {
            console.log("Permiso de notificaciones denegado");
            return null;
        }
    } catch (error) {
        console.error("Error al pedir permisos:", error);
        return null;
    }
}

// Escuchar mensajes en primer plano
export function escucharMensajes() {
    const messaging = getMessaging(app);
    onMessage(messaging, (payload) => {
        console.log("Mensaje recibido:", payload);

        Swal.fire({
            icon: "info",
            title: payload.notification.title || "Nueva notificación",
            text: payload.notification.body || ""
        });
    });
}