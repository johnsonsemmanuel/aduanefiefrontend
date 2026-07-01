const fs = require("fs");
const path = require("path");

function patchReactPackage(name) {
  const pkgPath = path.resolve(
    process.cwd(),
    "node_modules",
    name,
    "package.json",
  );
  if (!fs.existsSync(pkgPath)) {
    console.log(`  ${name}: not found, skipping`);
    return;
  }
  const pkg = JSON.parse(fs.readFileSync(pkgPath, "utf8"));
  if (pkg.exports?.["."]?.["react-server"]) {
    delete pkg.exports["."]["react-server"];
    fs.writeFileSync(pkgPath, JSON.stringify(pkg, null, 2));
    console.log(`  ✓ ${name}: removed react-server condition`);
  } else {
    console.log(`  ${name}: no react-server condition found`);
  }
}

console.log("Patching React exports to remove react-server condition...");
patchReactPackage("react");
patchReactPackage("react-dom");
