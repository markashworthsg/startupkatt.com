// Version-guarded build entrypoint (kept ES5 so it parses on legacy Node).
//
// Vite 6 / Tailwind v4 require Node 18+. The Forge host runs Node 8, so if a
// deploy script still calls `npm run build` there, we must NOT crash. The
// compiled assets are already committed under public/build. On modern Node
// (local / CI) we run the real Vite build.
var cp = require('child_process');

var major = parseInt(process.versions.node.split('.')[0], 10);

if (isNaN(major) || major < 18) {
  console.log(
    '[build] Node ' + process.versions.node +
    ' is too old for Vite; using committed assets in public/build. Skipping build.'
  );
  process.exit(0);
}

// node_modules/.bin is on PATH because npm invokes this via `npm run build`.
cp.execSync('vite build', { stdio: 'inherit' });
