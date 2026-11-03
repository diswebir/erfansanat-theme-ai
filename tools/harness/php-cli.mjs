// Minimal PHP CLI runner on php-wasm for the harness (no server).
// usage: node php-cli.mjs <script.php> [args...]  |  echo '<?php ...' | node php-cli.mjs -
import { PHP } from '@php-wasm/universal';
import { loadNodeRuntime, useHostFilesystem } from '@php-wasm/node';
import fs from 'node:fs';
import path from 'node:path';

const [,, script, ...args] = process.argv;
const id = await loadNodeRuntime('8.3', { emscriptenOptions: { processId: 42 } });
const php = new PHP(id);
await useHostFilesystem(php);

const code = script === '-' ? fs.readFileSync(0, 'utf8') : fs.readFileSync(script, 'utf8');
const res = await php.run({ code, argv: [script, ...args].map(String) });
if (res.text) process.stdout.write(res.text);
if (res.errors) process.stderr.write('\n[stderr] ' + res.errors + '\n');
process.exit(res.exitCode || 0);
