<?php
/*
 * The "Enjoying vcita?" request for a WordPress.org review.
 *
 * It shows only on a connected site, on the Plugins page and on the plugin's own pages, and only
 * to administrators. It is part of the page HTML, so it does not move the page when it appears.
 * Each administrator's answer is saved in their user meta, so it holds across browsers:
 * - "Rate 5 Stars" and "already rated" hide it until the next plugin version.
 * - "maybe later" hides it for 2 days.
 * - The close button hides it for this page view only.
 *
 * Up to 4.6.6 the answer was kept in the browser's localStorage, and closing it saved nothing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPSHD_VCITA_RATING_META', 'wpshd_vcita_rating' );
define( 'WPSHD_VCITA_REVIEW_URL', 'https://wordpress.org/support/plugin/meeting-scheduler-by-vcita/reviews/#new-post' );

add_action( 'admin_enqueue_scripts', 'wpshd_vcita_rating_assets' );
add_action( 'admin_notices', 'wpshd_vcita_rating_notice_on_plugins_page' );
add_action( 'wp_ajax_wpshd_vcita_rating', 'wpshd_vcita_ajax_rating' );

function wpshd_vcita_rating_due() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}
	$wpshd_vcita_widget = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
	if ( empty( $wpshd_vcita_widget['uid'] ) || empty( $wpshd_vcita_widget['success'] ) ) {
		return false;
	}
	$state = get_user_meta( get_current_user_id(), WPSHD_VCITA_RATING_META, true );
	if ( ! is_array( $state ) ) {
		return true;
	}
	if ( ! empty( $state['done_version'] ) && WPSHD_VCITA_WIDGET_VERSION === $state['done_version'] ) {
		return false;
	}
	return empty( $state['snoozed_until'] ) || time() >= (int) $state['snoozed_until'];
}

/** The Plugins page, or one of this plugin's pages. Their hook names contain the plugin folder. */
function wpshd_vcita_rating_screen( $hook ) {
	return 'plugins.php' === $hook || false !== strpos( $hook, WPSHD_VCITA_WIDGET_UNIQUE_ID );
}

function wpshd_vcita_rating_assets( $hook ) {
	if ( ! wpshd_vcita_rating_screen( $hook ) || ! wpshd_vcita_rating_due() ) {
		return;
	}
	wp_enqueue_style( 'wpshd-vcita-rating', plugins_url( 'assets/style/rating_v.css', __FILE__ ), array(), WPSHD_VCITA_WIDGET_VERSION );
	wp_enqueue_script( 'wpshd-vcita-rating', plugins_url( 'assets/js/rating_v.js', __FILE__ ), array(), WPSHD_VCITA_WIDGET_VERSION, true );
	wp_localize_script(
		'wpshd-vcita-rating',
		'wpshdVcitaRating',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'wpshd_vcita_nonce_action' ),
		)
	);
}

function wpshd_vcita_rating_notice_on_plugins_page() {
	if ( 'plugins' === get_current_screen()->id && wpshd_vcita_rating_due() ) {
		wpshd_vcita_render_rating( 'notice' );
	}
}

/**
 * The plugin's pages call this from their header, as a card like the page's other cards. The
 * Plugins page gets it as an admin notice.
 *
 * @param string $context 'notice' on the Plugins page, 'page' on the plugin's pages.
 */
function wpshd_vcita_render_rating( $context = 'page' ) {
	if ( 'notice' === $context ) {
		wpshd_vcita_render_rating_notice();
		return;
	}
	// The plugin's cards are the direct children of .vcita-wrap-inner, which gives them their width,
	// background and border. This card uses the same wrapper and markup to look like them.
	?>
	<div class="vcita-wrap-inner wpshd-vcita-rating-wrap">
		<div class="vcita__page__inner__section wpshd-vcita-rating-card" data-wpshd-vcita-rating role="region" aria-label="<?php esc_attr_e( 'Rate the plugin', 'meeting-scheduler-by-vcita' ); ?>">
			<h3>
				<?php esc_html_e( 'Enjoying vcita?', 'meeting-scheduler-by-vcita' ); ?>
				<small><?php esc_html_e( 'Help other small business owners by giving us a rating!', 'meeting-scheduler-by-vcita' ); ?></small>
			</h3>
			<div class="wpshd-vcita-rating-actions">
				<a href="<?php echo esc_url( WPSHD_VCITA_REVIEW_URL ); ?>" target="_blank" rel="noopener" class="vcita__btn__blue" data-wpshd-vcita-rating-op="rate"><?php esc_html_e( 'Rate 5 Stars', 'meeting-scheduler-by-vcita' ); ?></a>
				<button type="button" class="wpshd-vcita-rating-link" data-wpshd-vcita-rating-op="later"><?php esc_html_e( 'maybe later', 'meeting-scheduler-by-vcita' ); ?></button>
				<button type="button" class="wpshd-vcita-rating-link" data-wpshd-vcita-rating-op="rated"><?php esc_html_e( 'already rated', 'meeting-scheduler-by-vcita' ); ?></button>
			</div>
			<button type="button" class="wpshd-vcita-rating-close" data-wpshd-vcita-rating-op="close">
				<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'meeting-scheduler-by-vcita' ); ?></span>
			</button>
		</div>
	</div>
	<?php
}

/** The Plugins page version: a WordPress admin notice. */
function wpshd_vcita_render_rating_notice() {
	?>
	<div class="wpshd-vcita-rating notice" data-wpshd-vcita-rating role="region" aria-label="<?php esc_attr_e( 'Rate the plugin', 'meeting-scheduler-by-vcita' ); ?>">
		<img src="<?php echo esc_url( plugins_url( 'images/settings.png', __FILE__ ) ); ?>" alt="" class="wpshd-vcita-rating-icon">
		<div class="wpshd-vcita-rating-body">
			<p class="wpshd-vcita-rating-title"><?php esc_html_e( 'Enjoying vcita?', 'meeting-scheduler-by-vcita' ); ?></p>
			<p class="wpshd-vcita-rating-text"><?php esc_html_e( 'Help other small business owners by giving us a rating!', 'meeting-scheduler-by-vcita' ); ?></p>
			<div class="wpshd-vcita-rating-actions">
				<a href="<?php echo esc_url( WPSHD_VCITA_REVIEW_URL ); ?>" target="_blank" rel="noopener" class="wpshd-vcita-rating-btn" data-wpshd-vcita-rating-op="rate"><?php esc_html_e( 'Rate 5 Stars', 'meeting-scheduler-by-vcita' ); ?></a>
				<button type="button" class="wpshd-vcita-rating-link" data-wpshd-vcita-rating-op="later"><?php esc_html_e( 'maybe later', 'meeting-scheduler-by-vcita' ); ?></button>
				<button type="button" class="wpshd-vcita-rating-link" data-wpshd-vcita-rating-op="rated"><?php esc_html_e( 'already rated', 'meeting-scheduler-by-vcita' ); ?></button>
			</div>
		</div>
		<button type="button" class="wpshd-vcita-rating-close" data-wpshd-vcita-rating-op="close">
			<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'meeting-scheduler-by-vcita' ); ?></span>
		</button>
	</div>
	<?php
}

function wpshd_vcita_ajax_rating() {
	check_ajax_referer( 'wpshd_vcita_nonce_action', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( null, 403 );
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked by check_ajax_referer() above
	$op = isset( $_POST['op'] ) ? sanitize_key( wp_unslash( $_POST['op'] ) ) : '';
	if ( 'later' === $op ) {
		$state = array( 'snoozed_until' => time() + 2 * DAY_IN_SECONDS );
	} elseif ( 'rate' === $op || 'rated' === $op ) {
		$state = array( 'done_version' => WPSHD_VCITA_WIDGET_VERSION );
	} else {
		wp_send_json_error( null, 400 );
	}
	update_user_meta( get_current_user_id(), WPSHD_VCITA_RATING_META, $state );
	wp_send_json_success();
}
