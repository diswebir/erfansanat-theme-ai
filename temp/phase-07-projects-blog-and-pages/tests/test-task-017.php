<?php
/**
 * P07-T017 — blog comments: core form/list hooks, nesting, moderation, closed state.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T017', 'دیدگاه‌های وبلاگ' );
es_check( 'target: comments.php', es_file( 'comments.php' ) );
es_check( 'target: template-parts/post/comments.php', es_file( 'template-parts/post/comments.php' ) );
es_check( 'AC-01: wp_list_comments/comment_form هسته', es_contains( 'template-parts/post/comments.php', 'wp_list_comments(' ) && es_contains( 'template-parts/post/comments.php', 'comment_form(' ) );
es_check( 'وابستگی T012: هوک دیدگاه به comments_template وصل است', es_contains( 'functions.php', 'es_single_post_comments' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	update_option( 'thread_comments', 1 );
	update_option( 'thread_comments_depth', 3 );
	update_option( 'comment_registration', 0 );
	update_option( 'comment_moderation', 0 );
	update_option( 'require_name_email', 1 );

	$pid = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله دیدگاه‌دار', 'post_name' => 'comments-017-' . uniqid(), 'comment_status' => 'open', 'post_content' => 'متن' ) );
	$parent = (int) wp_insert_comment( array( 'comment_post_ID' => $pid, 'comment_author' => 'کاربر آزمایشی', 'comment_author_email' => 'user017@example.com', 'comment_content' => 'دیدگاه والد آزمایشی', 'comment_approved' => 1, 'comment_type' => 'comment' ) );
	$reply = (int) wp_insert_comment( array( 'comment_post_ID' => $pid, 'comment_author' => 'مدیر سایت', 'comment_author_email' => 'admin017@example.com', 'comment_content' => 'پاسخ مدیر به دیدگاه', 'comment_parent' => $parent, 'comment_approved' => 1, 'user_id' => 1, 'comment_type' => 'comment' ) );

	$render = static function ( $post_id ) {
		$q = new WP_Query( array( 'p' => $post_id, 'post_type' => 'post' ) );
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
		ob_start();
		comments_template();
		return ob_get_clean();
	};
	$html = $render( $pid );
	es_check( 'SC-01: دیدگاه والد و پاسخ مدیر رندر می‌شوند', false !== strpos( $html, 'دیدگاه والد آزمایشی' ) && false !== strpos( $html, 'پاسخ مدیر به دیدگاه' ) );
	es_check( 'AC-02: پاسخ تودرتو با depth هسته و ترتیب درست', false !== strpos( $html, 'depth-2' ) && strpos( $html, 'پاسخ مدیر به دیدگاه' ) > strpos( $html, 'دیدگاه والد آزمایشی' ) && false !== strpos( $html, 'data-belowelement="comment-' . $parent . '"' ) );
	es_check( 'AC-01: فرم هسته با فیلدهای استاندارد', false !== strpos( $html, 'id="commentform"' ) && false !== strpos( $html, 'name="comment"' ) && false !== strpos( $html, 'comment_post_ID' ) );
	es_check( 'AC-03: دکمه ارسال با design system', false !== strpos( $html, 'bg-emerald-700' ) && false !== strpos( $html, 'rounded-xl' ) );
	$hook_fired = 0;
	add_action( 'comment_form_before', static function () use ( &$hook_fired ) { $hook_fired++; } );
	$render( $pid );
	es_check( 'AC-01: اکشن comment_form_before شلیک می‌شود', $hook_fired > 0 );
	es_check( 'AC-02: لینک پاسخ هسته حاضر است', false !== strpos( $html, 'comment-reply-link' ) );

	// Moderation: pending comment shows the awaiting message, not the content twice.
	$pending = (int) wp_insert_comment( array( 'comment_post_ID' => $pid, 'comment_author' => 'کاربر منتظر', 'comment_author_email' => 'pending017@example.com', 'comment_content' => 'متن در انتظار تأیید', 'comment_approved' => 0, 'comment_type' => 'comment' ) );
	$GLOBALS['comment'] = get_comment( $pending );
	ob_start();
	es_comment_callback( get_comment( $pending ), array( 'style' => 'div', 'avatar_size' => 44 ), 1 );
	$pending_html = ob_get_clean();
	es_check( 'AC-02: دیدگاه در انتظار پیام moderation می‌دهد', false !== strpos( $pending_html, 'در انتظار تأیید' ) );
	$GLOBALS['comment'] = null;

	// Closed comments.
	$closed = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله بسته', 'post_name' => 'closed-017-' . uniqid(), 'comment_status' => 'closed', 'post_content' => 'متن' ) );
	$closed_html = $render( $closed );
	es_check( 'SC-02: دیدگاه بسته پیام درست می‌دهد', false !== strpos( $closed_html, 'دیدگاه‌ها بسته شده است' ) && false === strpos( $closed_html, 'id="commentform"' ) );

	// Protected post outputs nothing.
	$prot = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'رمزدار', 'post_name' => 'prot-017-' . uniqid(), 'comment_status' => 'open', 'post_password' => 'x' ) );
	es_check( 'نوشته رمزدار بخش دیدگاه نمی‌دهد', '' === trim( $render( $prot ) ) );

	// Single post wiring: full render contains the comment form.
	$q = new WP_Query( array( 'p' => $pid, 'post_type' => 'post' ) );
	$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
	ob_start(); include ES_THEME . '/single.php'; $single = ob_get_clean();
	es_check( 'تک‌نوشته کامل فرم دیدگاه دارد', false !== strpos( $single, 'id="commentform"' ) && false !== strpos( $single, 'دیدگاه والد آزمایشی' ) );

	wp_reset_postdata();
	foreach ( array( $parent, $reply, $pending ) as $cid ) { wp_delete_comment( $cid, true ); }
	foreach ( array( $pid, $closed, $prot ) as $id ) { wp_delete_post( $id, true ); }
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
