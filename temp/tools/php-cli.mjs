#!/usr/bin/env node
/**
 * PHP CLI bridge for the Erfan Sanat theme harness.
 * Runs real PHP 8.4 (WASM build from @php-wasm/node) against the host filesystem.
 * Usage: node php-cli.mjs [php args...]   (e.g. node php-cli.mjs -l file.php)
 */
import { PHP } from '@php-wasm/universal';
import { loadNodeRuntime, useHostFilesystem } from '@php-wasm/node';

const args = process.argv.slice(2);
const runtime = await loadNodeRuntime('8.4', {
  emscriptenOptions: { processId: process.pid },
});
const php = new PHP(runtime);
useHostFilesystem(php);

const response = await php.cli(['php', ...args], { cwd: process.cwd() });

const pump = async (stream, target) => {
  const reader = stream.getReader();
  try {
    for (;;) {
      const { done, value } = await reader.read();
      if (done) break;
      target.write(Buffer.from(value));
    }
  } catch {
    /* stream closed */
  }
};

await Promise.all([
  pump(response.stdout, process.stdout),
  pump(response.stderr, process.stderr),
]);
const code = await response.exitCode;
process.exit(code);
