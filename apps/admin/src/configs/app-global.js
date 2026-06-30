// project settings, you can change only PROJECT_NAME, BASE_URL and WEBSITE_URL otherwise it can break the app
export const PROJECT_NAME = 'Aduanefie marketplace';
export const BASE_URL =
  process.env.REACT_APP_BASE_URL || 'https://api.aduanefie.org';
export const WEBSITE_URL = 'https://aduanefie.org';
export const api_url = BASE_URL + '/api/v1/';
export const api_url_admin = BASE_URL + '/api/v1/dashboard/admin/';
export const api_url_admin_dashboard = BASE_URL + '/api/v1/dashboard/';
export const IMG_URL = '';
export const export_url = BASE_URL + '/storage/';
export const example = BASE_URL + '/';

// map api key, ypu can get it from https://console.cloud.google.com/apis/library
export const MAP_API_KEY = 'MAP_API_KEY';

// firebase keys, remember to change to your own keys here and in file public/firebase-messaging-sw.js
export const VAPID_KEY = 'VAPID_KEY';
export const API_KEY = 'API_KEY';
export const AUTH_DOMAIN = 'AUTH_DOMAIN';
export const PROJECT_ID = 'PROJECT_ID';
export const STORAGE_BUCKET = 'STORAGE_BUCKET';
export const MESSAGING_SENDER_ID = 'MESSAGING_SENDER_ID';
export const APP_ID = 'APP_ID';
export const MEASUREMENT_ID = 'MEASUREMENT_ID';

// recaptcha key, you can get it from https://www.google.com/recaptcha/admin/create
export const RECAPTCHASITEKEY = 'RECAPTCHASITEKEY';

// demo data, no need to change
export const LAT = 47.4143302506288;
export const LNG = 8.532059477976883;
export const DEMO_SELLER = 334; // seller_id
export const DEMO_SELLER_UUID = '3566bdf6-3a09-4488-8269-70a19f871bd0'; // seller_id
export const DEMO_SHOP = 599; // seller_id
export const DEMO_DELIVERYMAN = 375; // courier_id
export const DEMO_MANEGER = 114; // courier_id
export const DEMO_MODERATOR = 297; // courier_id
export const DEMO_ADMIN = 107; // courier_id
export const SUPPORTED_FORMATS = [
  'image/jpg',
  'image/jpeg',
  'image/png',
  'image/svg+xml',
  'image/svg',
];
