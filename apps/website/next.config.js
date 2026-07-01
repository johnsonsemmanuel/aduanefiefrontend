/** @type {import('next').NextConfig} */
const path = require("path");
const fs = require("fs");

const withBundleAnalyzer = require("@next/bundle-analyzer")({
  enabled: process.env.ANALYZE === "true",
});

// Patch React's exports to remove "react-server" condition BEFORE any bundler
// resolves modules. This ensures require("react") always gets the full client
// entry where hooks (useMemo, useId, etc.) work during SSR.
function removeReactServerCondition(name) {
  const paths = [
    require.resolve(name + "/package.json"),
  ];
  // Also check next/dist/compiled/<name>
  try {
    const nextCompiled = require.resolve(
      "next/dist/compiled/" + name + "/package.json",
    );
    paths.push(nextCompiled);
  } catch {}

  for (const pkgPath of paths) {
    try {
      const pkg = JSON.parse(fs.readFileSync(pkgPath, "utf8"));
      let changed = false;
      // Patch the "." export
      if (pkg.exports?.["."]?.["react-server"]) {
        delete pkg.exports["."]["react-server"];
        changed = true;
      }
      // Also patch sub-entry exports (server, server.edge, etc.)
      for (const key of Object.keys(pkg.exports || {})) {
        if (pkg.exports[key]?.["react-server"]) {
          delete pkg.exports[key]["react-server"];
          changed = true;
        }
      }
      if (changed) {
        fs.writeFileSync(pkgPath, JSON.stringify(pkg, null, 2) + "\n");
        console.log(
          "  ✓ Patched " +
            pkgPath.replace(process.cwd(), ".") +
            ": removed react-server conditions",
        );
      }
    } catch (e) {
      console.error("  ✗ Failed to patch", pkgPath, e.message);
    }
  }
}

console.log("Patching React exports for SSR compatibility...");
removeReactServerCondition("react");
removeReactServerCondition("react-dom");

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
