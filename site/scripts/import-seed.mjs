// One-command seed import for a fresh Sanity project.
//
// Usage:
//   PUBLIC_SANITY_PROJECT_ID=xxx PUBLIC_SANITY_DATASET=production \
//   SANITY_WRITE_TOKEN=sk... npm run import-seed
//
// Creates-or-replaces every seed document with a stable _id, so re-running
// is safe and idempotent.

import { createClient } from '@sanity/client';
import { allSeedDocuments } from '../src/seed/index.ts';

const {
  PUBLIC_SANITY_PROJECT_ID: projectId,
  PUBLIC_SANITY_DATASET: dataset = 'production',
  SANITY_WRITE_TOKEN: token,
} = process.env;

const missing = [];
if (!projectId || projectId === 'pamoja-placeholder') {
  missing.push('PUBLIC_SANITY_PROJECT_ID (a real project id, not the placeholder)');
}
if (!dataset) missing.push('PUBLIC_SANITY_DATASET');
if (!token) missing.push('SANITY_WRITE_TOKEN');

if (missing.length > 0) {
  console.error(
    `Cannot import seed content — missing environment variables:\n  - ${missing.join('\n  - ')}\n\n` +
      'Set them and re-run, e.g.:\n' +
      '  PUBLIC_SANITY_PROJECT_ID=abc123 PUBLIC_SANITY_DATASET=production SANITY_WRITE_TOKEN=sk... npm run import-seed'
  );
  process.exit(1);
}

const client = createClient({
  projectId,
  dataset,
  token,
  apiVersion: '2025-01-01',
  useCdn: false,
});

console.log(
  `Importing ${allSeedDocuments.length} seed documents into project ${projectId}, dataset "${dataset}"...`
);

let failures = 0;
for (const doc of allSeedDocuments) {
  try {
    await client.createOrReplace(doc);
    console.log(`  ok  ${doc._type}/${doc._id}`);
  } catch (err) {
    failures += 1;
    console.error(`  FAIL ${doc._type}/${doc._id}: ${err.message}`);
  }
}

if (failures > 0) {
  console.error(`\n${failures} document(s) failed to import.`);
  process.exit(1);
}

console.log('\nDone. All seed documents are in place.');
