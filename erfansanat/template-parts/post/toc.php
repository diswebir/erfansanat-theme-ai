<?php
/**
 * Single-post table of contents (P07-T013).
 *
 * Prepare stage runs before the_content() so rendered blocks/shortcodes are
 * scanned; the filter injects unique, scroll-offset ids into real h2/h3 only.
 * Render stage is attached to es_single_post_sidebar_top and outputs nothing
 * when there are no valid headings or the post is password protected.
 *
 * @package ErfanSanat
 * @var array $args { @type bool $prepare }
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'es_post_toc_state' ) ) {
	/** @return array{headings:array<int,array{level:int,text:string,id:string}>,used:array<string,bool>,post_id:int,filter_added:bool,action_added:bool} */
	function &es_post_toc_state() {
		static $state = array( 'headings' => array(), 'used' => array(), 'post_id' => 0, 'filter_added' => false, 'action_added' => false );
		return $state;
	}

	function es_post_toc_prepare( $post_id ) {
		$state = &es_post_toc_state();
		$state['headings'] = array();
		$state['used']     = array();
		$state['post_id']  = (int) $post_id;
		if ( ! $state['filter_added'] ) {
			add_filter( 'the_content', 'es_post_toc_filter', 20, 1 );
			$state['filter_added'] = true;
		}
		if ( ! $state['action_added'] ) {
			add_action( 'es_single_post_sidebar_top', static function () { get_template_part( 'template-parts/post/toc' ); } );
			$state['action_added'] = true;
		}
	}

	function es_post_toc_filter( $content ) {
		$state = &es_post_toc_state();
		if ( ! $state['post_id'] || (int) get_the_ID() !== $state['post_id'] || post_password_required( $state['post_id'] ) ) {
			return $content;
		}
		$state['headings'] = array();
		$state['used']     = array();

		return preg_replace_callback(
			'/<h([23])\b([^>]*)>(.*?)<\/h\1>/is',
			static function ( $m ) use ( &$state ) {
				$level = (int) $m[1];
				$attrs = $m[2];
				$text  = trim( wp_strip_all_tags( $m[3] ) );
				$text  = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( $text, ENT_QUOTES, 'UTF-8' ) ) );
				if ( '' === $text ) {
					return $m[0];
				}
				$id = '';
				if ( preg_match( '/\bid\s*=\s*(["\'])(.*?)\1/i', $attrs, $idm ) ) {
					$id = trim( $idm[2] );
				} elseif ( preg_match( '/\bid\s*=\s*([^\s>]+)/i', $attrs, $idm ) ) {
					$id = trim( $idm[1], '"\'' );
				}
				if ( '' === $id ) {
					$id = 'sec-' . ( count( $state['headings'] ) + 1 );
				}
				$base = $id;
				$n    = 2;
				while ( isset( $state['used'][ $id ] ) ) {
					$id = $base . '-' . $n++;
				}
				$state['used'][ $id ] = true;

				$attrs = preg_replace( '/\bid\s*=\s*(["\']).*?\1/i', '', $attrs );
				$attrs = preg_replace( '/\bid\s*=\s*[^\s>]+/i', '', $attrs );
				$class = 'scroll-mt-24';
				if ( preg_match( '/\bclass\s*=\s*(["\'])(.*?)\1/i', $attrs, $cm ) ) {
					if ( false === strpos( $cm[2], 'scroll-mt-24' ) ) {
						$class = trim( $cm[2] . ' scroll-mt-24' );
					} else {
						$class = $cm[2];
					}
					$attrs = str_replace( $cm[0], '', $attrs );
				}
				$state['headings'][] = array( 'level' => $level, 'text' => $text, 'id' => $id );

				return '<h' . $level . trim( $attrs ) . ' id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '">' . $m[3] . '</h' . $level . '>';
			},
			$content
		);
	}

	function es_post_toc_render() {
		$state = &es_post_toc_state();
		if ( ! $state['headings'] || post_password_required( $state['post_id'] ) ) {
			return;
		}
		?>
		<div>
			<nav class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-xs" aria-label="<?php esc_attr_e( 'سرفصل‌های مقاله', 'erfansanat' ); ?>">
				<h4 class="mb-4 flex items-center gap-2 text-[15px] font-extrabold text-slate-900"><svg aria-hidden="true" class="lucide lucide-list h-4.5 w-4.5 text-emerald-700" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M3 5h.01"></path><path d="M3 12h.01"></path><path d="M3 19h.01"></path><path d="M8 5h13"></path><path d="M8 12h13"></path><path d="M8 19h13"></path></svg><?php esc_html_e( 'سرفصل‌های مقاله', 'erfansanat' ); ?></h4>
				<ul class="space-y-2.5 border-r border-emerald-100 pr-4">
					<?php foreach ( $state['headings'] as $es_heading ) : ?>
						<li><a class="block text-[13px] font-medium leading-6 text-slate-600 transition-colors hover:text-emerald-700 <?php echo 3 === $es_heading['level'] ? 'pr-3' : ''; ?>" href="#<?php echo esc_attr( $es_heading['id'] ); ?>"><?php echo esc_html( $es_heading['text'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>
		<?php
	}
}

if ( ! empty( $args['prepare'] ) ) {
	es_post_toc_prepare( get_the_ID() );
	return;
}

es_post_toc_render();
