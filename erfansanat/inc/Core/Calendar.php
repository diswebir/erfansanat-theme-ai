<?php
/**
 * Persian calendar display without a plugin dependency (P07-T011).
 *
 * Uses the site's local post date (not UTC) and the well-tested civil-date
 * arithmetic underlying the Jalaali calendar. When WP-Parsidate is present,
 * its filtered get_the_date() output is respected to avoid double conversion.
 * Machine-readable dates must continue using get_the_date('c') directly.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class Calendar {

	private const MONTHS = array( 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند' );
	private const DIGITS = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );

	/**
	 * Gregorian civil date → Jalali [year, month, day].
	 * Valid for the modern Persian calendar range used by WordPress content.
	 *
	 * @return array{0:int,1:int,2:int}
	 */
	public static function to_jalali( int $year, int $month, int $day ): array {
		$offsets = array( 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 );
		$carry   = $month > 2 ? $year + 1 : $year;
		$days    = 355666 + 365 * $year + intdiv( $carry + 3, 4 ) - intdiv( $carry + 99, 100 ) + intdiv( $carry + 399, 400 ) + $day + $offsets[ $month - 1 ];
		$jy      = -1595 + 33 * intdiv( $days, 12053 );
		$days   %= 12053;
		$jy     += 4 * intdiv( $days, 1461 );
		$days   %= 1461;
		if ( $days > 365 ) {
			$jy  += intdiv( $days - 1, 365 );
			$days = ( $days - 1 ) % 365;
		}
		if ( $days < 186 ) {
			$jm = 1 + intdiv( $days, 31 );
			$jd = 1 + $days % 31;
		} else {
			$jm = 7 + intdiv( $days - 186, 30 );
			$jd = 1 + ( $days - 186 ) % 30;
		}

		return array( $jy, $jm, $jd );
	}

	/** Format a site-local date with Persian months/digits (j,d,n,m,Y,y,F,M). */
	public static function format( \DateTimeInterface $date, string $format = 'j F Y' ): string {
		list( $jy, $jm, $jd ) = self::to_jalali( (int) $date->format( 'Y' ), (int) $date->format( 'n' ), (int) $date->format( 'j' ) );
		$values = array(
			'j' => (string) $jd,
			'd' => str_pad( (string) $jd, 2, '0', STR_PAD_LEFT ),
			'n' => (string) $jm,
			'm' => str_pad( (string) $jm, 2, '0', STR_PAD_LEFT ),
			'Y' => (string) $jy,
			'y' => substr( (string) $jy, -2 ),
			'F' => self::MONTHS[ $jm - 1 ],
			'M' => self::MONTHS[ $jm - 1 ],
		);
		$out    = '';
		$length = strlen( $format );
		for ( $i = 0; $i < $length; $i++ ) {
			$char = $format[ $i ];
			if ( '\\' === $char && $i + 1 < $length ) {
				$out .= $format[ ++$i ];
			} else {
				$out .= $values[ $char ] ?? $char;
			}
		}

		return strtr( $out, self::DIGITS );
	}

	/** Whether the existing Persian-date plugin already localizes WordPress dates. */
	public static function has_parsidate(): bool {
		return defined( 'WP_PARSI_VERSION' ) || defined( 'WP_PARSI_VER' ) || function_exists( 'parsidate' ) || function_exists( 'parsidate_date' );
	}

	/** Published/display date in the site timezone; never converts a machine date. */
	public static function post_date( int $post_id, string $format = 'j F Y' ): string {
		if ( self::has_parsidate() ) {
			return (string) get_the_date( $format, $post_id );
		}
		$date = get_post_datetime( $post_id, 'date', 'site' );

		return $date ? self::format( $date, $format ) : '';
	}
}
