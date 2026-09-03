// Minimal static file server for the built site (dist/). No dependencies.
// Usage: node scripts/serve-static.mjs [port]   (default 8080)
import { createServer } from 'node:http';
import { readFile, stat } from 'node:fs/promises';
import { join, normalize, extname } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = fileURLToPath(new URL('../dist', import.meta.url));
const port = Number(process.argv[2]) || 8080;

const types = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8',
  '.mjs': 'text/javascript; charset=utf-8',
  '.json': 'application/json',
  '.xml': 'application/xml',
  '.txt': 'text/plain; charset=utf-8',
  '.svg': 'image/svg+xml',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.webp': 'image/webp',
  '.woff': 'font/woff',
  '.woff2': 'font/woff2',
  '.ico': 'image/x-icon',
};

async function resolve(pathname) {
  // Prevent path traversal.
  const clean = normalize(pathname).replace(/^(\.\.[/\\])+/, '');
  let file = join(root, clean);
  if (!file.startsWith(root)) return null;
  try {
    const s = await stat(file);
    if (s.isDirectory()) file = join(file, 'index.html');
  } catch {
    file = join(file, 'index.html');
  }
  try {
    await stat(file);
    return file;
  } catch {
    // SPA fallback for the embedded Sanity studio.
    if (clean.startsWith('/studio')) return join(root, 'studio', 'index.html');
    return null;
  }
}

createServer(async (req, res) => {
  const url = new URL(req.url || '/', 'http://x');
  const pathname = decodeURIComponent(url.pathname);
  const file = await resolve(pathname);
  if (!file) {
    const notFound = join(root, '404.html');
    res.writeHead(404, { 'content-type': types['.html'] });
    res.end(await readFile(notFound).catch(() => 'Not found'));
    return;
  }
  const ext = extname(file);
  const headers = { 'content-type': types[ext] || 'application/octet-stream' };
  // Long cache for hashed build assets, short for HTML.
  headers['cache-control'] =
    pathname.startsWith('/_astro/') ? 'public, max-age=31536000, immutable' : 'public, max-age=300';
  res.writeHead(200, headers);
  res.end(await readFile(file));
}).listen(port, '::', () => {
  console.log(`Serving ${root} on http://[::]:${port} (dual-stack)`);
});
