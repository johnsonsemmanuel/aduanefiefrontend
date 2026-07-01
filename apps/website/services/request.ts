//@ts-nocheck
import axios from "axios";
import i18n from "i18n";
import { API_URL } from "constants/constants";
import { getPackerieFromBrowser, removePackerie } from "utils/session";
import { error as toastError } from "components/alert/toast";

const isBrowser = typeof window !== "undefined";

const request = axios.create({
  baseURL: API_URL,
  // timeout: 16000,
});

request.interceptors.request.use(
  (config) => {
    if (isBrowser) {
      const token = getPackerieFromBrowser("access_token");
      if (token) {
        config.headers.Authorization = token;
      }
    }
    const locale = i18n.language;
    config.params = { lang: locale, ...config.params };
    return config;
  },

  (error) => errorHandler(error),
);

function errorHandler(error) {
  if (error?.response) {
    if (error?.response?.status === 403) {
    } else if (error?.response?.status === 401) {
      if (isBrowser) {
        toastError(i18n.t("unauthorized"), {
          toastId: "unauthorized",
        });
        removePackerie("user");
        removePackerie("access_token");
        window.location.replace("/login");
      }
    } else if (error?.response?.status === 404) {
      console.log("404 Not Found:", error?.config?.url);
    }
  }
  console.log("error => ", error);

  return Promise.reject(error.response);
}

request.interceptors.response.use((response) => response.data, errorHandler);

export default request;
