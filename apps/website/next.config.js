/** @type {import('next').NextConfig} */
const path = require("path");
const fs = require("fs");

const withBundleAnalyzer = require("@next/bundle-analyzer")({
  enabled: process.env.ANALYZE === "true",
});

// Replace react.react-server.js and react-dom.react-server.js file contents
// with the full client entry (index.js), so NO MATTER how the bundler resolves
// require("react"), hooks (useMemo, useId, etc.) always get the full dispatcher.
function replaceReactServerEntry(name) {
  const dirs = [];

  // User's node_modules/<name>
  try {
    dirs.push(path.dirname(require.resolve(name + "/package.json")));
  } catch {}

  // next/dist/compiled/<name>
  try {
    dirs.push(
      path.dirname(
        require.resolve("next/dist/compiled/" + name + "/package.json"),
      ),
    );
  } catch {}

  for (const dir of dirs) {
    try {
      const reactServerFile = path.join(dir, name + ".react-server.js");
      const indexFile = path.join(dir, "index.js");

      if (fs.existsSync(reactServerFile) && fs.existsSync(indexFile)) {
        // Replace react.react-server.js content with index.js content
        fs.copyFileSync(indexFile, reactServerFile);
        console.log(
          "  ✓ Replaced " +
            reactServerFile.replace(process.cwd(), ".") +
            " with full client entry",
        );
      }

      // Also handle the CJS files
      for (const env of ["production", "development"]) {
        const cjsReactServer = path.join(dir, "cjs", name + ".react-server." + env + ".js");
        const cjsIndex = path.join(dir, "cjs", name + "." + env + ".js");
        if (fs.existsSync(cjsReactServer) && fs.existsSync(cjsIndex)) {
          fs.copyFileSync(cjsIndex, cjsReactServer);
          console.log(
            "  ✓ Replaced " +
              cjsReactServer.replace(process.cwd(), ".") +
              " with full client entry",
          );
        }
      }
    } catch (e) {
      console.error("  ✗ Failed to patch", dir, e.message);
    }
  }
}

console.log("Patching React server entries to use full client entries...");
replaceReactServerEntry("react");
replaceReactServerEntry("react-dom");

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
      // Remove "react-server" condition from webpack's condition names
      // as a defense-in-depth measure
      config.resolve.conditionNames = (
        config.resolve.conditionNames || []
      ).filter((name) => name !== "react-server");
    }
    return config;
  },
};

module.exports = withBundleAnalyzer(nextConfig);
