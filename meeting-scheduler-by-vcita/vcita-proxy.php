<?php
/*
 * The calls to scheduler-proxy, the service that holds this site's vcita token, and the
 * sign-in that connects the site to a vcita business.
 *
 * The calls run on this server, not in the browser. Up to 4.6.6 the admin pages called the
 * proxy from the browser, which would put a per-site proxy secret in the page. The proxy
 * does not issue one yet. When it does, wpshd_vcita_proxy_get() sends it, and nothing in the
 * browser has to change.
 *
 * The sign-in:
 * 1. The site registers its callback URL with the proxy once and gets a site id (wp_id).
 * 2. Connect opens the vcita sign-in with the proxy's OAuth client and state = wp_id.
 * 3. The proxy keeps the token and sends the browser to <callback>actions/auth with the
 *    signed-in business. Anyone can call that URL, and anyone can compute wp_id from the
 *    site's URL. So wpshd_vcita_process_action() accepts the result only when the proxy
 *    names the same business (CVE-2026-14433), and wpshd_vcita_callback_refusal() is empty.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPSHD_VCITA_PROXY_SECRET_OPTION', 'wpshd_vcita_proxy_secret' );
define( 'WPSHD_VCITA_CONNECT_PENDING', 'wpshd_vcita_connect_pending' );
// A sign-up creates the vcita account first, which can take several minutes.
define( 'WPSHD_VCITA_CONNECT_WINDOW', HOUR_IN_SECONDS );

add_action( 'wp_ajax_wpshd_vcita_proxy', 'wpshd_vcita_ajax_proxy' );
add_action( 'wp_ajax_wpshd_vcita_connect_url', 'wpshd_vcita_ajax_connect_url' );

function wpshd_vcita_proxy_url() {
	return WPSHD_VCITA_USE_MEET2KNOW
		? 'https://us-central1-scheduler-272415.cloudfunctions.net/scheduler-proxy-meet2know'
		: 'https://us-central1-scheduler-272415.cloudfunctions.net/scheduler-proxy';
}

/** The proxy's OAuth client at vcita. It is public: it is in every sign-in URL. */
function wpshd_vcita_oauth_client_id() {
	return WPSHD_VCITA_USE_MEET2KNOW
		? 'a27f3c7639bf18840038cce2acb231111e69b9b4167c694c5e62224294abb1ff'
		: 'cc46751cc204610d8e62157d61c1a50fe59299a2384bde258cf85e60b1f55ea8';
}

/**
 * The URL the proxy returns the sign-in result to. It must stay byte for byte what 4.6.6 sent,
 * because the proxy derives wp_id from it.
 */
function wpshd_vcita_callback_url() {
	return rest_url( 'vcita-wordpress/v1' );
}

/**
 * GET from the proxy. Returns array( 'code' => HTTP status, 'body' => decoded JSON or null ),
 * or a WP_Error when the proxy cannot be reached.
 */
function wpshd_vcita_proxy_get( $path ) {
	$args   = array( 'timeout' => 15 );
	$secret = wpshd_vcita_proxy_secret();
	if ( '' !== $secret ) {
		$args['headers'] = array( 'Authorization' => 'Bearer ' . $secret );
	}

	$response = wp_remote_get( wpshd_vcita_proxy_url() . $path, $args );
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	return array(
		'code' => (int) wp_remote_retrieve_response_code( $response ),
		'body' => is_array( $body ) ? $body : null,
	);
}

/** The site id at the proxy. Registers the site on first use. Returns '' on failure. */
function wpshd_vcita_site_id() {
	$wpshd_vcita_widget = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
	if ( ! empty( $wpshd_vcita_widget['wp_id'] ) ) {
		return (string) $wpshd_vcita_widget['wp_id'];
	}

	$result = wpshd_vcita_proxy_get( '/install?callback=' . rawurlencode( wpshd_vcita_callback_url() ) );
	if ( is_wp_error( $result ) || empty( $result['body']['success'] ) || empty( $result['body']['wp_id'] ) || ! is_string( $result['body']['wp_id'] ) ) {
		return '';
	}

	// Read again: another request can change the option while this one waits for the proxy.
	$wpshd_vcita_widget          = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
	$wpshd_vcita_widget['wp_id'] = sanitize_text_field( $result['body']['wp_id'] );
	update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
	return $wpshd_vcita_widget['wp_id'];
}

/** The lang parameter of the vcita sign-in: "en-gb" for British English, else the language code. */
function wpshd_vcita_sign_in_lang() {
	$locale = get_locale();
	if ( 'en_GB' === $locale ) {
		return 'en-gb';
	}
	$parts = explode( '_', $locale );
	return $parts[0];
}

/**
 * The URL that starts a vcita sign-in, or a sign-up followed by the sign-in.
 *
 * A sign-up must not pass registration=true to /app/oauth/authorize: vcita then goes to the
 * marketing sign-up page and drops client_id, redirect_uri and state. Without state the proxy
 * cannot tell which site to connect. So a sign-up goes to the sign-up page with the whole
 * authorize URL in redirect_to.
 */
function wpshd_vcita_authorize_url( $wp_id, $signup, $logout, $email ) {
	$lang  = wpshd_vcita_sign_in_lang();
	$query = 'response_type=code&client_id=' . wpshd_vcita_oauth_client_id() . '&lang=' . rawurlencode( $lang );
	if ( $signup ) {
		$query .= '&intent=' . rawurlencode( '["add_online_scheduling"]' );
	}
	if ( $logout ) {
		$query .= '&logout=true';
	}
	$query .= '&redirect_uri=' . rawurlencode( wpshd_vcita_proxy_url() . '/callback' )
		. '&state=' . rawurlencode( $wp_id )
		. '&invite=' . WPSHD_VCITA_WIDGET_INVITE_CODE;
	if ( '' !== $email ) {
		$query .= '&email=' . rawurlencode( $email );
	}

	$path = '/app/oauth/authorize?' . $query;
	if ( $signup ) {
		return 'https://www.' . WPSHD_VCITA_SERVER_BASE . '/signup?redirect_to=' . rawurlencode( $path )
			. '&invite=' . WPSHD_VCITA_WIDGET_INVITE_CODE . '&lang=' . rawurlencode( $lang );
	}
	return 'https://app.' . WPSHD_VCITA_SERVER_BASE . $path;
}

function wpshd_vcita_check_ajax() {
	if ( false === check_ajax_referer( 'wpshd_vcita_nonce_action', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
		wp_send_json(
			array(
				'error'   => true,
				'message' => __( 'Your session has expired. Reload the page and try again.', 'meeting-scheduler-by-vcita' ),
			),
			403
		);
	}
}

function wpshd_vcita_unreachable() {
	wp_send_json(
		array(
			'error'   => true,
			'message' => __( 'vcita could not be reached. Please check your connection and try again.', 'meeting-scheduler-by-vcita' ),
		),
		502
	);
}

/**
 * Passes a proxy read through to the admin page. The response is the proxy's own JSON and
 * status, as the browser got them before 4.6.7.
 */
function wpshd_vcita_ajax_proxy() {
	wpshd_vcita_check_ajax();
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked in wpshd_vcita_check_ajax()
	$resource = isset( $_POST['resource'] ) ? sanitize_key( wp_unslash( $_POST['resource'] ) ) : '';

	$wp_id = wpshd_vcita_site_id();
	if ( '' === $wp_id ) {
		wpshd_vcita_unreachable();
	}
	if ( 'install' === $resource ) {
		wp_send_json( array( 'success' => true, 'wp_id' => $wp_id ) );
	}
	if ( ! in_array( $resource, array( 'business', 'availability', 'services', 'staff' ), true ) ) {
		wp_send_json( array( 'error' => true, 'message' => 'Unknown request' ), 400 );
	}

	$result = wpshd_vcita_proxy_get( '/' . $resource . '/' . rawurlencode( $wp_id ) );
	if ( is_wp_error( $result ) || null === $result['body'] ) {
		wpshd_vcita_unreachable();
	}
	wp_send_json( $result['body'], $result['code'] );
}

/** Starts a Connect: records who started it, and returns the vcita sign-in URL. */
function wpshd_vcita_ajax_connect_url() {
	wpshd_vcita_check_ajax();

	$wp_id = wpshd_vcita_site_id();
	if ( '' === $wp_id ) {
		wpshd_vcita_unreachable();
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked in wpshd_vcita_check_ajax()
	$signup = isset( $_POST['signup'] ) && '1' === $_POST['signup'];
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked in wpshd_vcita_check_ajax()
	$logout = isset( $_POST['logout'] ) && '1' === $_POST['logout'];
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked in wpshd_vcita_check_ajax()
	$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	set_transient( WPSHD_VCITA_CONNECT_PENDING, get_current_user_id(), WPSHD_VCITA_CONNECT_WINDOW );
	wp_send_json( array( 'url' => wpshd_vcita_authorize_url( $wp_id, $signup, $logout, $email ) ) );
}

/**
 * Why a sign-in result must be refused, or '' to accept it.
 *
 * In a real Connect, an administrator clicked Connect within WPSHD_VCITA_CONNECT_WINDOW, and the
 * sign-in returns in that administrator's browser. A result that an attacker sends, after
 * signing in to their own vcita account with this site's wp_id as state, fails one of these.
 *
 * The REST API ignores the WordPress cookie unless the request has a REST nonce, and the
 * proxy's redirect cannot have one. So this reads the logged_in cookie itself. A top-level
 * navigation sends it, because WordPress sets it without SameSite (Lax).
 */
function wpshd_vcita_callback_refusal() {
	$pending_user = (int) get_transient( WPSHD_VCITA_CONNECT_PENDING );
	if ( $pending_user <= 0 ) {
		return 'no Connect was started in the last ' . ( WPSHD_VCITA_CONNECT_WINDOW / MINUTE_IN_SECONDS ) . ' minutes';
	}
	$browser_user = (int) wp_validate_auth_cookie( '', 'logged_in' );
	if ( $browser_user <= 0 ) {
		return 'the browser is not signed in to this site';
	}
	if ( $browser_user !== $pending_user || ! user_can( $browser_user, 'manage_options' ) ) {
		return 'the browser is signed in as a different user from the one who clicked Connect';
	}
	return '';
}

/** Writes to the debug log when WP_DEBUG is on. It never logs request data. */
function wpshd_vcita_log( $message ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- debug mode only
		error_log( 'meeting-scheduler-by-vcita: ' . $message );
	}
}

/*
 * The per-site proxy secret.
 *
 * It is kept in its own option, not in WPSHD_VCITA_WIDGET_KEY: the admin pages print that
 * option into the page. It is not autoloaded, and it is encrypted with a key made from the
 * salts in wp-config.php, so a copy of the database alone does not reveal it. If the salts
 * change, it cannot be decrypted, and the site has to connect again.
 *
 * Nothing saves it yet: the proxy does not issue secrets. How the proxy delivers it decides
 * where wpshd_vcita_save_proxy_secret() is called.
 */

function wpshd_vcita_secret_key() {
	return sodium_crypto_generichash( AUTH_KEY . SECURE_AUTH_KEY, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES );
}

function wpshd_vcita_save_proxy_secret( $secret ) {
	$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
	$box   = sodium_crypto_secretbox( $secret, $nonce, wpshd_vcita_secret_key() );
	// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- binary ciphertext stored as text
	update_option( WPSHD_VCITA_PROXY_SECRET_OPTION, base64_encode( $nonce . $box ), false );
}

/** The secret, or '' when there is none or it cannot be decrypted. */
function wpshd_vcita_proxy_secret() {
	$stored = get_option( WPSHD_VCITA_PROXY_SECRET_OPTION, '' );
	if ( ! is_string( $stored ) || '' === $stored || ! function_exists( 'sodium_crypto_secretbox_open' ) ) {
		return '';
	}
	// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- see wpshd_vcita_save_proxy_secret()
	$raw = base64_decode( $stored, true );
	if ( false === $raw || strlen( $raw ) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ) {
		return '';
	}
	$plain = sodium_crypto_secretbox_open(
		substr( $raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ),
		substr( $raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ),
		wpshd_vcita_secret_key()
	);
	return false === $plain ? '' : $plain;
}

function wpshd_vcita_forget_proxy_secret() {
	delete_option( WPSHD_VCITA_PROXY_SECRET_OPTION );
}
