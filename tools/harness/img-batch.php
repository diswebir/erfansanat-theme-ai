<?php
/**
 * One-shot image batch (GD). The task list is inlined because this runtime
 * does not reliably pass argv/env to the script.
 * Regenerate the list before running: see tools/harness/make-img-batch.py
 */
declare(strict_types=1);

$root = '/home/user/erfansanat-theme-ai/theme/';
$jobs = json_decode( (string) file_get_contents( __DIR__ . '/img-jobs.json' ), true );

$total_in = 0;
$total_out = 0;

foreach ( (array) $jobs as $job ) {
	$cmd = $job['cmd'] ?? 'webp';
	$src = $root . $job['src'];
	$dst = $root . ltrim( str_replace( '..', '', $job['dst'] ), '/' );
	if ( 'png' === $cmd || 'poster' === $cmd ) {
		$img = imagecreatefromjpeg( $src );
		if ( ! $img ) { echo "DECODE_FAIL {$job['src']}\n"; continue; }
		$w = (int) ( $job['w'] ?? 1200 );
		$h = (int) ( $job['h'] ?? 900 );
		$c = imagecreatetruecolor( $w, $h );
		// cover-crop
		$sw = imagesx( $img ); $sh = imagesy( $img );
		$scale = max( $w / $sw, $h / $sh );
		$dw = (int) ceil( $sw * $scale ); $dh = (int) ceil( $sh * $scale );
		imagecopyresampled( $c, $img, (int) ( ( $w - $dw ) / 2 ), (int) ( ( $h - $dh ) / 2 ), 0, 0, $dw, $dh, $sw, $sh );
		imagepng( $c, $dst, 9 );
		imagedestroy( $c ); imagedestroy( $img );
		echo "PNG {$job['dst']} " . filesize( $dst ) . " bytes\n";
		continue;
	}
	if ( 'webp' !== $cmd ) {
		continue;
	}

	if ( ! is_file( $src ) ) {
		echo "MISSING {$job['src']}\n";
		continue;
	}

	$img = imagecreatefromjpeg( $src );
	if ( ! $img ) {
		echo "DECODE_FAIL {$job['src']}\n";
		continue;
	}

	@mkdir( dirname( $dst ), 0755, true );
	$ok = imagewebp( $img, $dst, (int) ( $job['q'] ?? 82 ) );
	imagedestroy( $img );

	if ( ! $ok ) {
		echo "ENCODE_FAIL {$job['src']}\n";
		continue;
	}

	$in  = filesize( $src );
	$out = filesize( $dst );
	$total_in  += $in;
	$total_out += $out;

	printf( "%-26s %7.0fKB -> %6.0fKB  (%d%%)\n", $job['src'], $in / 1024, $out / 1024, (int) round( 100 * $out / max( 1, $in ) ) );
}

printf( "\nTOTAL %7.0fKB -> %6.0fKB  saved %.0f%%\n", $total_in / 1024, $total_out / 1024, 100 - ( 100 * $total_out / max( 1, $total_in ) ) );
