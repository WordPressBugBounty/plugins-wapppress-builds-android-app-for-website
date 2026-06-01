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

    // Safe read (PHP 8+)
    $start_page_id = isset($settings['start_url']) ? (int) $settings['start_url'] : 0;

    // Resolve URL
    if ( $start_page_id > 0 ) {
        $start_url = get_permalink( $start_page_id );
    }

    // Fallback to site URL
    if ( empty($start_url) ) {
        $start_url = home_url('/');
    }

    // Force HTTPS
    $start_url = INSTANTAPPY_pwa_httpsify( $start_url );

    // Relative manifest URL
    if ( $rel === true ) {
        $path = wp_parse_url( $start_url, PHP_URL_PATH );
        $start_url = empty($path) ? '.' : $path;

        return apply_filters( 'INSTANTAPPY_manifest_start_url', $start_url );
    }

    return $start_url;
}


/**
 * Convert Website http URL to https
 *
 */
function INSTANTAPPY_pwa_httpsify( $url ) {
	return str_replace( 'http://', 'https://', $url );
}

/**
 * Check if Progessive Web App is ready
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
 
 /**
 * INSTANTAPPY-Filesystem related funtions
 */
/**
 * Initialize WP filesystem
 */
function INSTANTAPPY_filesystem_initializer() {
	
	global $wp_filesystem;
	
	if ( empty( $wp_filesystem ) ) {
		require_once( trailingslashit( ABSPATH ) . 'wp-admin/includes/file.php' );
		WP_Filesystem();
	}
}

/**
 * Write to a file using WP_Filesystem() functions
 */
function INSTANTAPPY_put_pwa_contents( $file, $content = null ) {
	
	// Return false if no filename is provided
	if ( empty( $file ) ) {
		return false;
	}
	
	// Initialize the WP filesystem
	INSTANTAPPY_filesystem_initializer();
	global $wp_filesystem;
	if( ! $wp_filesystem->put_contents( $file, $content, 0644) ) {
		return false;
	}
	
	return true;
}

/**
 * Read contents of a file using WP_Filesystem() functions
 */
function INSTANTAPPY_get_contents( $file, $array = false ) {
	
	// Return false if no filename is provided
	if ( empty( $file ) ) {
		return false;
	}
	
	// Initialize the WP filesystem
	INSTANTAPPY_filesystem_initializer();
	global $wp_filesystem;
	
	// Reads entire file into a string
	if ( $array == false ) {
		return $wp_filesystem->get_contents( $file );
	}
	
	// Reads entire file into an array
	return $wp_filesystem->get_contents_array( $file );
}

/**
 * Delete a file
 */
function INSTANTAPPY_delete( $file ) {
	
	// Return false if no filename is provided
	if ( empty( $file ) ) {
		return false;
	}
	
	// Initialize the WP filesystem
	INSTANTAPPY_filesystem_initializer();
	global $wp_filesystem;
	
	return $wp_filesystem->delete( $file );
}

/**
 * INSTANTAPPY- Functions for checking WordPress multisites compatibility
*
 */
/**
 * 
 * Will return (string) as current blog ID in case of multisite or an empty string will be returend
 * 
 */
function INSTANTAPPY_multisite_handler() {
	
	
	if ( ! is_multisite() ) {
		return ''; // Return empty string if not a multisite
	}
	
	return '-' . get_current_blog_id();
}

/**
 * Save status for current blog id
 *
 */
function INSTANTAPPY_multisite_activation_status( $status ) {
	
	// Section for multisites
	if ( ! is_multisite() || ! isset( $status ) ) {
		return;
	}
	
	$INSTANTAPPY_sites = get_site_option( 'INSTANTAPPY_active_sites', array() ); // Grab list list of sites where INSTANTAPPY is activated.
	
	$INSTANTAPPY_sites[ get_current_blog_id() ] = $status; // Set the status
	
	update_site_option( 'INSTANTAPPY_active_sites', $INSTANTAPPY_sites ); // Save to the database.
}

/**
 * Handle multisite deactivation
 * 
 * Deletes manifest and service worker fro all sub-sites.
 */
function INSTANTAPPY_multisite_network_deactivator() {
	
	if ( wp_is_large_network() ) {
		return; // Don't run on large networks
	}
	
	$INSTANTAPPY_sites = get_site_option( 'INSTANTAPPY_active_sites' ); // Grab the list of blog ids 
	
	// Go through each active site.
	foreach( $INSTANTAPPY_sites as $blog_id => $actviation_status ) 
	{
		
		// Switch through each blog
		switch_to_blog( $blog_id );
		
		// Delete pwa manifest
		INSTANTAPPY_delete_pwa_manifest();
	
		// Delete pwa service worker
		INSTANTAPPY_delete_sw();
		
		/**
		 * Delete INSTANTAPPY version info of current blog.
		 */
		delete_option( 'INSTANTAPPY_version' );
	
		// Save deactivation status of current blog.
		INSTANTAPPY_multisite_activation_status( false );
		
		// Return to back main site
		restore_current_blog();
	}
}