/* شیم PHP v6.1 — php-wasm 3.1.62؛ خروجی run در {bytes, errors, exitCode} */
const path = require('path');
const fs = require('fs');
const { loadNodeRuntime, createNodeFsMountHandler } = require('/home/user/tools/phpwasm/node_modules/@php-wasm/node');
const { PHP } = require('/home/user/tools/phpwasm/node_modules/@php-wasm/universal');

function flush(resp) {
	const dec = new TextDecoder();
	if (resp.bytes) process.stdout.write(dec.decode(resp.bytes));
	if (resp.errors) process.stderr.write(resp.errors);
}

async function main() {
	const argv = process.argv.slice(2);
	let lintFile = null;
	let runFile = null;
	for (let i = 0; i < argv.length; i++) {
		if (argv[i] === '-l') lintFile = argv[++i];
		else if (argv[i] === '-f') runFile = argv[++i];
		else if (!runFile) runFile = argv[i];
	}
	const php = new PHP(await loadNodeRuntime('8.4', { emscriptenOptions: { processId: process.pid } }));
	await php.mount('/host', createNodeFsMountHandler('/'));

	if (lintFile) {
		const abs = path.resolve(lintFile);
		const esc = ('/host' + abs).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
		const wpath = '/home/user/tools/__lint.php';
		fs.writeFileSync(wpath, "<?php\n$es_toks = token_get_all(file_get_contents('" + esc + "'));\necho 'No syntax errors detected (lex-only) in " + esc + "';\n");
		php.defineConstant('ES_SCRIPT_PATH', '/host' + wpath);
		const resp = await php.run({ scriptPath: '/host' + wpath });
		flush(resp);
		process.exit(resp.exitCode || 0);
	}

	if (!runFile) {
		process.stderr.write('usage: php [-l file | -f file]\n');
		process.exit(2);
	}
	const abs = path.resolve(runFile);
	if (!fs.existsSync(abs)) {
		process.stderr.write("Could not open input file: " + abs + "\n");
		process.exit(254);
	}
	php.defineConstant('ES_SCRIPT_PATH', '/host' + abs);
	const resp = await php.run({ scriptPath: '/host' + abs });
	flush(resp);
	process.exit(resp.exitCode || 0);
}

main().catch((e) => {
	process.stderr.write(String(e && e.message ? e.message : e) + '\n');
	process.exit(253);
});
