<?php
/**
 * Global helpers: es_option() with a static, self-invalidating cache.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme {

	defined( 'ABSPATH' ) || exit;

	use ErfanSanat\Theme\Options\Schema;
	use ErfanSanat\Theme\Options\Store;

	final class Helpers {

		/** @var array<string,mixed>|null */
		private static ?array $cache = null;

		private static bool $hooks_registered = false;

		/**
		 * Read a theme option with a static per-request cache.
		 *
		 * @param mixed $default Returned when the key is unknown.
		 * @return mixed
		 */
		public static function option( string $key, $default = null ) {
			self::register_hooks();

			if ( ! Schema::is_known( $key ) ) {
				return $default;
			}

			if ( null === self::$cache ) {
				self::$cache = Store::raw();
			}

			if ( array_key_exists( $key, self::$cache ) ) {
				return self::$cache[ $key ];
			}

			$schema_default = Schema::default( $key );

			return null === $schema_default ? $default : $schema_default;
		}

		/**
		 * Invalidate the static cache (called after any option write).
		 */
		public static function flush(): void {
			self::$cache = null;
		}

		/**
		 * Whether the cache is currently warm (used by tests).
		 */
		public static function is_cached(): bool {
			return null !== self::$cache;
		}

		private static function register_hooks(): void {
			if ( self::$hooks_registered ) {
				return;
			}

			self::$hooks_registered = true;

			add_action( 'erfansanat_options_updated', array( self::class, 'flush' ), 10, 0 );
			add_action( 'updated_option', array( self::class, 'maybe_flush' ), 10, 1 );
			add_action( 'deleted_option', array( self::class, 'maybe_flush' ), 10, 1 );
		}

		/**
		 * Flush only when our single option is touched directly.
		 *
		 * @param string $option Option name.
		 */
		public static function maybe_flush( $option ): void {
			if ( Store::OPTION === $option ) {
				self::flush();
			}
		}
	}
}

namespace {

	if ( ! function_exists( 'es_option' ) ) {
		/**
		 * Read a theme option (static-cached, schema-aware).
		 *
		 * @param mixed $default Fallback for unknown keys.
		 * @return mixed
		 */
		function es_option( string $key, $default = null ) {
			return \ErfanSanat\Theme\Helpers::option( $key, $default );
		}
	}
}

namespace {

	if ( ! function_exists( 'es_comment_callback' ) ) {
		/**
		 * Design-system comment renderer (P07-T017). Used as wp_list_comments
		 * callback; the core walker still appends nested children. Pending
		 * comments show the standard moderation notice.
		 *
		 * @param WP_Comment $comment Comment object.
		 * @param array      $args    List args.
		 * @param int        $depth   Nesting depth (1-based).
		 */
		function es_comment_callback( $comment, $args, $depth ) {
			$es_pending = '0' === (string) $comment->comment_approved;
			$es_classes = ( $es_pending ? 'awaiting-moderation ' : '' ) . ( (int) $comment->user_id > 0 ? 'bypostauthor' : '' );
			?>
			<div id="comment-<?php comment_ID(); ?>" <?php comment_class( $es_classes, $comment ); ?>>
				<div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-4 sm:p-5">
					<div class="flex items-center gap-3">
						<span class="overflow-hidden rounded-full border border-emerald-100"><?php echo get_avatar( $comment, (int) ( $args['avatar_size'] ?? 44 ), '', get_comment_author( $comment ), array( 'class' => 'h-11 w-11 rounded-full object-cover' ) ); ?></span>
						<div class="min-w-0">
							<div class="truncate text-[13.5px] font-extrabold text-slate-900"><?php echo esc_html( get_comment_author( $comment ) ); ?><?php if ( (int) $comment->user_id > 0 ) : ?> <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10.5px] font-bold text-emerald-700"><?php esc_html_e( 'تیم عرفان صنعت', 'erfansanat' ); ?></span><?php endif; ?></div>
							<time class="text-[11.5px] font-semibold text-slate-400" datetime="<?php echo esc_attr( get_comment_date( 'c', $comment ) ); ?>"><?php echo esc_html( \ErfanSanat\Theme\Core\Calendar::format( new DateTimeImmutable( get_comment_date( 'Y-m-d', $comment ) ), 'j F Y' ) ); ?></time>
						</div>
					</div>
					<?php if ( $es_pending ) : ?>
						<p class="mt-3 rounded-xl bg-amber-50 px-3.5 py-2.5 text-[12.5px] font-bold text-amber-700"><?php esc_html_e( 'دیدگاه شما در انتظار تأیید است.', 'erfansanat' ); ?></p>
					<?php else : ?>
						<div class="comment-content mt-3 text-[13.5px] leading-8 text-slate-700"><?php comment_text( $comment ); ?></div>
						<?php if ( comments_open( $comment->comment_post_ID ) ) : ?>
							<div class="mt-2 text-[12px] font-bold text-emerald-700"><?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ?? (int) get_option( 'thread_comments_depth', 3 ), 'reply_text' => __( 'پاسخ', 'erfansanat' ) ) ), $comment ); ?></div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php
			// Note: no closing div — the core walker closes it after children.
		}
	}

	if ( ! function_exists( 'es_comment_field_classes' ) ) {
		/**
		 * Style the core author/email/url fields without removing any field
		 * or hook (P07-T017 AC-01).
		 */
		function es_comment_field_classes( $fields ) {
			$input = 'w-full rounded-2xl border border-slate-200 bg-slate-50/60 px-4 py-3 text-[13.5px] text-slate-800 outline-none transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10';
			foreach ( array( 'author', 'email', 'url' ) as $key ) {
				if ( isset( $fields[ $key ] ) ) {
					$fields[ $key ] = preg_replace( '/(<input[^>]*?)class="([^"]*)"/', '$1class="$2 ' . esc_attr( $input ) . '"', $fields[ $key ], 1 );
					if ( null !== $fields[ $key ] && false === strpos( $fields[ $key ], $input ) ) {
						$fields[ $key ] = str_replace( '<input', '<input class="' . esc_attr( $input ) . '"', $fields[ $key ] );
					}
				}
			}
			return $fields;
		}
		add_filter( 'comment_form_default_fields', 'es_comment_field_classes' );
	}
}
