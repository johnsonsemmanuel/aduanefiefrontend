const fs = require("fs");
const path = require("path");

function patchReactPackage(name) {
  // User's node_modules/react
  const pkgPath = path.resolve(
    process.cwd(),
    "node_modules",
    name,
    "package.json",
  );
  if (fs.existsSync(pkgPath)) {
    const pkg = JSON.parse(fs.readFileSync(pkgPath, "utf8"));
    if (pkg.exports?.["."]?.["react-server"]) {
      delete pkg.exports["."]["react-server"];
      fs.writeFileSync(pkgPath, JSON.stringify(pkg, null, 2));
      console.log(`  ✓ ${name} (user): removed react-server condition`);
    } else {
      console.log(`  ${name} (user): no react-server condition found`);
    }
  } else {
    console.log(`  ${name} (user): not found, skipping`);
  }

  // Next.js prebundled next/dist/compiled/react
  const compiledPath = path.resolve(
    process.cwd(),
    "node_modules",
    "next",
    "dist",
    "compiled",
    name,
    "package.json",
  );
  if (fs.existsSync(compiledPath)) {
    const pkg = JSON.parse(fs.readFileSync(compiledPath, "utf8"));
    if (pkg.exports?.["."]?.["react-server"]) {
      delete pkg.exports["."]["react-server"];
      fs.writeFileSync(compiledPath, JSON.stringify(pkg, null, 2));
      console.log(`  ✓ ${name} (next/compiled): removed react-server condition`);
    } else {
      console.log(
        `  ${name} (next/compiled): no react-server condition found`,
      );
    }
  } else {
    console.log(`  ${name} (next/compiled): not found, skipping`);
  }
}

console.log("Patching React exports to remove react-server condition...");
patchReactPackage("react");
patchReactPackage("react-dom");
