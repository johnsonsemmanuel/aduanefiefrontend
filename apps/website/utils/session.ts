import packerie from "js-packerie";
import nextPackerie from "next-packeries";

const isBrowser = typeof window !== "undefined";

export const getPackerieFromBrowser = (key: string): any => {
  return packerie.get(key);
};

export const getPackerieFromServer = (ctx: any, key = "id_token") => {
  const packerie = nextPackerie(ctx);
  const token = packerie && packerie[key] ? packerie[key] : false;
  if (!token) {
    return null;
  }
  return token;
};

export const getPackerie = (key: string, context?: any) => {
  return isBrowser
    ? getPackerieFromBrowser(key)
    : getPackerieFromServer(context, key);
};

export const setPackerie = (key: string, token: any) => {
  packerie.set(key, token, { expires: 7 });
};

export const removePackerie = (key: string) => {
  packerie.remove(key);
};
