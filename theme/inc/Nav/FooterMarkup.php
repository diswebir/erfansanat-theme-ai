<?php
/** Dashboard-backed footer text with static DOM retained. */

namespace ErfanSanat\Theme\Nav;

defined( 'ABSPATH' ) || exit;

/** Replaces footer content safely without changing the static structure. */
final class FooterMarkup {

	/** Render the trusted static footer after applying dashboard text options. */
	public static function render( string $html ): string {
		$address = self::option( 'footer.address', 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹' );
		$phone = self::option( 'header.phone', '031-91091011' );
		$sales = self::option( 'header.sales_phone', '09137976915' );
		$email = self::option( 'header.email', 'info@erfansanat.com' );
		$hours = self::option( 'footer.work_hours', 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر' );
		$copyright = self::option( 'footer.copyright', '© تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.' );

		if ( '' === $address && '' === $phone && '' === $sales && '' === $email && '' === $hours ) {
			$html = self::remove_div_with( $html, 'اطلاعات تماس کارخانه' );
		} else {
			if ( '' === $address ) { $html = self::remove_div_with( $html, 'leading-6' ); }
			if ( '' === $phone ) { $html = self::remove_div_with( $html, 'tel:031-91091011' ); }
			if ( '' === $sales ) { $html = self::remove_div_with( $html, 'tel:09137976915' ); }
			if ( '' === $email ) { $html = self::remove_div_with( $html, 'mailto:info@erfansanat.com' ); }
			if ( '' === $hours ) { $html = self::remove_div_with( $html, 'شنبه تا پنجشنبه' ); }
		}

		$html = self::replace_span( $html, 'leading-6', $address );
		$html = self::replace_span( $html, 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر', $hours );
		$html = self::replace_text( $html, '© تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.', $copyright );
		$html = self::replace_phone_anchor( $html, '031-91091011', $phone, '۰۳۱ - ۹۱۰۹۱۰۱۱ (۱۰ خط ویژه)' );
		$html = self::replace_phone_anchor( $html, '09137976915', $sales, 'مدیر فروش (آقای محمدعلی قربانی): ۰۹۱۳۷۹۷۶۹۱۵' );
		$html = self::replace_email_anchor( $html, $email );
		return LegacyMarkup::rewrite_links( $html );
	}

	private static function option( string $key, string $default ): string {
		$value = function_exists( 'es_option' ) ? es_option( $key, $default ) : $default;
		return is_scalar( $value ) ? trim( (string) $value ) : '';
	}

	private static function escaped( string $value ): string {
		return function_exists( 'esc_html' ) ? esc_html( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}

	private static function replace_span( string $html, string $marker, string $value ): string {
		if ( '' === $value ) { return $html; }
		$escaped = self::escaped( $value );
		if ( 'leading-6' === $marker ) {
			return (string) preg_replace_callback( '/(<span class="leading-6">).*?(<\/span>)/s', static fn( $m ) => $m[1] . $escaped . $m[2], $html, 1 );
		}
		return str_replace( $marker, $escaped, $html );
	}

	private static function replace_text( string $html, string $old, string $new ): string {
		return '' !== $new ? str_replace( $old, self::escaped( $new ), $html ) : $html;
	}

	private static function replace_phone_anchor( string $html, string $old, string $phone, string $label ): string {
		$pattern = '/<a\s+href="tel:' . preg_quote( $old, '/' ) . '"([^>]*)>.*?<\/a>/s';
		if ( '' === $phone ) { return (string) preg_replace( $pattern, '', $html, 1 ); }
		$digits = preg_replace( '/[^0-9+]/', '', $phone );
		$shown = $phone === $old ? $label : self::escaped( $phone );
		return (string) preg_replace_callback( $pattern, static function ( $m ) use ( $digits, $shown ) {
			$url = function_exists( 'esc_attr' ) ? esc_attr( $digits ) : htmlspecialchars( $digits, ENT_QUOTES, 'UTF-8' );
			return '<a href="tel:' . $url . '"' . $m[1] . '>' . $shown . '</a>';
		}, $html, 1 );
	}

	private static function replace_email_anchor( string $html, string $email ): string {
		$pattern = '/<a\s+href="mailto:info@erfansanat\.com"([^>]*)>.*?<\/a>/s';
		if ( '' === $email ) { return (string) preg_replace( $pattern, '', $html, 1 ); }
		$safe = function_exists( 'sanitize_email' ) ? sanitize_email( $email ) : filter_var( $email, FILTER_SANITIZE_EMAIL );
		$escaped = function_exists( 'esc_attr' ) ? esc_attr( (string) $safe ) : htmlspecialchars( (string) $safe, ENT_QUOTES, 'UTF-8' );
		$shown = self::escaped( (string) $safe );
		return (string) preg_replace_callback( $pattern, static fn( $m ) => '<a href="mailto:' . $escaped . '"' . $m[1] . '>' . $shown . '</a>', $html, 1 );
	}

	/** Remove the nearest static div wrapper containing a unique contact marker. */
	private static function remove_div_with( string $html, string $marker ): string {
		$position = strpos( $html, $marker );
		if ( false === $position ) { return $html; }
		$start = strrpos( substr( $html, 0, $position ), '<div' );
		if ( false === $start ) { return $html; }
		$depth = 0;
		$tail = substr( $html, $start );
		if ( ! preg_match_all( '/<\/?div\b[^>]*>/i', $tail, $tokens, PREG_OFFSET_CAPTURE ) ) { return $html; }
		foreach ( $tokens[0] as $token ) {
			if ( 0 === strpos( $token[0], '</' ) ) { --$depth; } else { ++$depth; }
			if ( 0 === $depth ) {
				$end = $start + $token[1] + strlen( $token[0] );
				return substr( $html, 0, $start ) . substr( $html, $end );
			}
		}
		return $html;
	}
}
