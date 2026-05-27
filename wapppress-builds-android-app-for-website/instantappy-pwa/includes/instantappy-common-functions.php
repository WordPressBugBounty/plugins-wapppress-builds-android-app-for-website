<?php
/**
 * Common functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Return Start Page URL
 */
function INSTANTAPPY_get_pwa_start_url( $rel = false ) {

	$settings = INSTANTAPPY_grab_pwa_basic_settings();

	$start_page_id = isset( $settings['start_url'] ) ? (int) $settings['start_url'] : 0;

	$start_url = '';
	if ( $start_page_id > 0 ) {
		$start_url = get_permalink( $start_page_id );
	}

	if ( empty( $start_url ) ) {
		$start_url = home_url( '/' );
	}

	$start_url = INSTANTAPPY_pwa_httpsify( $start_url );

	if ( $rel === true ) {
		$path      = wp_parse_url( $start_url, PHP_URL_PATH );
		$start_url = empty( $path ) ? '.' : $path;
		return apply_filters( 'INSTANTAPPY_manifest_start_url', $start_url );
	}

	return $start_url;
}

/**
 * Convert http URL to https
 */
function INSTANTAPPY_pwa_httpsify( $url ) {
	return str_replace( 'http://', 'https://', $url );
}

/**
 * Check if PWA is ready
 */
function INSTANTAPPY_is_pwa_ready() {

	if (
		is_ssl() &&
		INSTANTAPPY_get_contents( INSTANTAPPY_manifest( 'abs' ) ) &&
		INSTANTAPPY_get_contents( INSTANTAPPY_PWA_service_worker( 'abs' ) )
	) {
		return apply_filters( 'INSTANTAPPY_is_pwa_ready', true );
	}

	return false;
}

/* ============================================================
   FILESYSTEM HELPERS
   Mirrors Super PWA exactly: plain WP_Filesystem(), no forced
   method. Forcing 'direct' breaks on many managed hosts.
   ============================================================ */

/**
 * Initialize the WP filesystem.
 */
function INSTANTAPPY_filesystem_initializer() {

	global $wp_filesystem;

	if ( empty( $wp_filesystem ) ) {
		require_once trailingslashit( ABSPATH ) . 'wp-admin/includes/file.php';
		WP_Filesystem();
	}
}

/**
 * Write content to a file.
 * Returns true on success, false on failure.
 */
function INSTANTAPPY_put_pwa_contents( $file, $content = null ) {

	if ( empty( $file ) ) {
		return false;
	}

	INSTANTAPPY_filesystem_initializer();
	global $wp_filesystem;

	if ( ! $wp_filesystem->put_contents( $file, $content, 0644 ) ) {
		return false;
	}

	return true;
}

/**
 * Read a file's contents.
 * Returns string, array of lines, or false on failure.
 */
function INSTANTAPPY_get_contents( $file, $array = false ) {

	if ( empty( $file ) ) {
		return false;
	}

	INSTANTAPPY_filesystem_initializer();
	global $wp_filesystem;

	if ( $array ) {
		return $wp_filesystem->get_contents_array( $file );
	}

	return $wp_filesystem->get_contents( $file );
}

/**
 * Delete a file.
 */
function INSTANTAPPY_delete( $file ) {

	if ( empty( $file ) ) {
		return false;
	}

	INSTANTAPPY_filesystem_initializer();
	global $wp_filesystem;

	return $wp_filesystem->delete( $file );
}

/* ============================================================
   MULTISITE HELPERS
   ============================================================ */

/**
 * Returns '-{blog_id}' on multisite, empty string on single site.
 */
function INSTANTAPPY_multisite_handler() {
	if ( ! is_multisite() ) {
		return '';
	}
	return '-' . get_current_blog_id();
}

/**
 * Save activation status for the current blog on multisite.
 */
function INSTANTAPPY_multisite_activation_status( $status ) {
	if ( ! is_multisite() || ! isset( $status ) ) {
		return;
	}

	$sites                            = get_site_option( 'INSTANTAPPY_active_sites', array() );
	$sites[ get_current_blog_id() ]   = $status;
	update_site_option( 'INSTANTAPPY_active_sites', $sites );
}

/**
 * Deactivate across all sub-sites (skip very large networks).
 */
function INSTANTAPPY_multisite_network_deactivator() {
	if ( wp_is_large_network() ) {
		return;
	}

	$sites = get_site_option( 'INSTANTAPPY_active_sites', array() );

	foreach ( $sites as $blog_id => $activation_status ) {
		switch_to_blog( $blog_id );

		INSTANTAPPY_delete_pwa_manifest();
		INSTANTAPPY_delete_sw();
		delete_option( 'INSTANTAPPY_version' );
		INSTANTAPPY_multisite_activation_status( false );

		restore_current_blog();
	}
}
