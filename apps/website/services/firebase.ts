import { getAuth } from "firebase/auth";
import { initializeApp } from "firebase/app";
import {
  API_KEY,
  APP_ID,
  AUTH_DOMAIN,
  MEASUREMENT_ID,
  MESSAGING_SENDER_ID,
  PROJECT_ID,
  STORAGE_BUCKET,
} from "constants/config";
import {
  getFirestore,
  collection,
  onSnapshot,
  query,
  orderBy,
  addDoc,
  serverTimestamp,
} from "firebase/firestore";
import { getStorage } from "firebase/storage";
import { store } from "redux/store";
import { setChats, setMessages } from "redux/slices/chat";
import { IChat, IMessage } from "interfaces";
import { error } from "components/alert/toast";

const isBrowser = typeof window !== "undefined";

const firebaseConfig = {
  apiKey: API_KEY,
  authDomain: AUTH_DOMAIN,
  projectId: PROJECT_ID,
  storageBucket: STORAGE_BUCKET,
  messagingSenderId: MESSAGING_SENDER_ID,
  appId: APP_ID,
  measurementId: MEASUREMENT_ID,
};

let app: ReturnType<typeof initializeApp> | undefined;
let auth: ReturnType<typeof getAuth> | undefined;
let storage: ReturnType<typeof getStorage> | undefined;
let db: ReturnType<typeof getFirestore> | undefined;

function initFirebase() {
  if (app) return;
  try {
    app = initializeApp(firebaseConfig);
    auth = getAuth(app);
    storage = getStorage(app);
    db = getFirestore(app);
  } catch (e) {
    console.warn("Firebase init failed:", e);
  }
}

if (isBrowser) {
  initFirebase();
}

export { auth, app as default };

export { storage, db };

export async function sendMessage(payload: IMessage) {
  try {
    if (!db) initFirebase();
    if (!db) return;
    await addDoc(collection(db, "messages"), {
      ...payload,
      created_at: serverTimestamp(),
    });
  } catch (err) {
    console.log("err => ", err);
    error("chat error");
  }
}

export async function createChat(payload: IChat) {
  try {
    if (!db) initFirebase();
    if (!db) return;
    await addDoc(collection(db, "chats"), {
      ...payload,
      created_at: serverTimestamp(),
    });
  } catch (err) {
    console.log("err => ", err);
    error("chat error");
  }
}
