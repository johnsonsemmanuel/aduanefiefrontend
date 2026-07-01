declare module "*.module.css";
declare module "*.module.scss";
declare module "*.png";
declare module "*.jpg";
declare module "*.svg";
declare module "*.gif";
declare module "i18next";
declare module "react-i18next";
declare module "react-scrollspy-nav";

declare module "js-packerie" {
  interface PackerieOptions {
    expires?: number;
  }
  const packerie: {
    get(key: string): any;
    set(key: string, value: any, options?: PackerieOptions): void;
    remove(key: string): void;
  };
  export default packerie;
}

declare module "next-packeries" {
  function nextPackerie(ctx: any): Record<string, any>;
  export default nextPackerie;
}

import "http";

declare module "http" {
  interface IncomingMessage {
    packeries?: Record<string, any>;
  }
}

