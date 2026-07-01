const isBrowser = typeof window !== "undefined";

function getBrowserCookie(name: string): string | null {
  const match = document.cookie.match(
    new RegExp(`(?:^|;\\s*)${name}=([^;]*)`),
  );
  return match ? decodeURIComponent(match[1]) : null;
}

function setBrowserCookie(name: string, value: string, days?: number): void {
  let expires = "";
  if (days) {
    const date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    expires = `; expires=${date.toUTCString()}`;
  }
  document.cookie = `${name}=${encodeURIComponent(value)}${expires}; path=/`;
}

function removeBrowserCookie(name: string): void {
  document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
}

function parseServerCookies(ctx: any): Record<string, string> {
  const cookieHeader = ctx?.req?.headers?.cookie || "";
  if (!cookieHeader) return {};
  return Object.fromEntries(
    cookieHeader.split(";").map((c: string) => {
      const [key, ...val] = c.trim().split("=");
      return [key.trim(), decodeURIComponent(val.join("="))];
    }),
  );
}

export const getPackerieFromBrowser = (key: string): any => {
  return getBrowserCookie(key);
};

export const getPackerieFromServer = (ctx: any, key = "id_token") => {
  const cookies = parseServerCookies(ctx);
  const token = cookies[key] || false;
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
  setBrowserCookie(key, token, 7);
};

export const removePackerie = (key: string) => {
  removeBrowserCookie(key);
};
