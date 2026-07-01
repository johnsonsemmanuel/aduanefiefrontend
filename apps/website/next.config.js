/** @type {import('next').NextConfig} */
const path = require("path");
const withBundleAnalyzer = require("@next/bundle-analyzer")({
  enabled: process.env.ANALYZE === "true",
});

const nextConfig = {
  serverExternalPackages: [
    "react-redux",
    "redux-persist",
    "react-query",
    "@emotion/react",
    "@emotion/styled",
    "@emotion/cache",
  ],
  reactStrictMode: false,
  typescript: {
    ignoreBuildErrors: false,
  },
  images: {
    remotePatterns: [
      {
        protocol: "https",
        hostname: process.env.NEXT_PUBLIC_API_HOSTNAME || "api.aduanefie.org",
      },
      {
        protocol: "https",
        hostname: process.env.NEXT_PUBLIC_STORAGE_HOSTNAME || "aduanefie.s3.amazonaws.com",
      },
      {
        protocol: "https",
        hostname: "demo-api.aduanefie.org",
      },
      {
        protocol: "https",
        hostname: "lh3.googleusercontent.com",
      },
    ],
    minimumCacheTTL: 3600,
    dangerouslyAllowSVG: true,
    contentSecurityPolicy: "default-src 'self'; script-src 'none'; sandbox;",
  },
  webpack: (config, { isServer }) => {
    if (isServer) {
      // Remove "react-server" condition so webpack resolves require("react")
      // to React's full client entry instead of the react-server stub
      config.resolve.conditionNames = (
        config.resolve.conditionNames || []
      ).filter((name) => name !== "react-server");
    }
    return config;
  },
};

module.exports = withBundleAnalyzer(nextConfig);
