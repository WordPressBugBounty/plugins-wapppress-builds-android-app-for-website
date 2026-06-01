<?php 
/**
* This will handle following following functions:
 * 1. functions related to the plugin action
 * 2. functions related to the plugin settings
 * 3. functions related to UI setup
 *
 */
/**
 * 1. functions related to the plugin action
 *
 */

// Exit if accessed directly
if ( ! defined('ABSPATH') ) exit;

function INSTANTAPPY_activate_plugin( $active_network ) {
	
	INSTANTAPPY_generate_pwa_manifest(); //Generate/write manifest with default option	
	
	INSTANTAPPY_generate_pwa_sw(); // Generate PWA service worker
	
	// If not network active
	if ( ! $active_network ) 
	{
		set_transient( 'INSTANTAPPY_notice_activation', true, 60 ); // Set transient for single site Notice
		return;
	}
	
	// If activated on a multisite.
	set_transient( 'INSTANTAPPY_network_admin_notice_activation', true, 60 );
}
register_activation_hook( INSTANTAPPY_PATH . 'INSTANTAPPY.php', 'INSTANTAPPY_activate_plugin' );

/**
 * Handling of  PWA Admin Notices
 *
 */
function INSTANTAPPY_admin_notices() {
	
	// PWA Notices
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
 
    // Notice on plugin activation
	if ( get_transient( 'INSTANTAPPY_notice_activation' ) ) {
	
		$INSTANTAPPY_is_ready = INSTANTAPPY_is_pwa_ready() ? 'Your PWA app is ready with the default settings. You can customize your app from settings section.' : '';
		

echo '<div class="updated notice is-dismissible"><p>';
   echo sprintf(
    /* translators: 1: App name, 2: Readiness message, 3: Customization URL */
    esc_html__(
        'Thank you for installing <strong>%1$s</strong> %2$s <a href="%3$s">Customize your app &rarr;</a>',
        'wapppress-builds-android-app-for-website'
    ),
    esc_html( INSTANTAPPY_NAME ),
    esc_html( $INSTANTAPPY_is_ready ),
    esc_url( admin_url( 'admin.php?page=INSTANTAPPY' ) )
);

echo '</p></div>';
		// Delete transient
		delete_transient( 'INSTANTAPPY_notice_activation' );
	}
	
	
}
add_action( 'admin_notices', 'INSTANTAPPY_admin_notices' );

/**
 * Network Admin 
 */
function INSTANTAPPY_network_admin_notices() {
	
	// Notices only for admins
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

// Network admin notice on multisite network activation
if ( get_transient( 'INSTANTAPPY_network_admin_notice_activation' ) ) {
    $INSTANTAPPY_is_ready = INSTANTAPPY_is_pwa_ready() 
        ? __( 'Your PWA app is ready on the main website with the default settings. You can customize your app from the settings section.', 'wapppress-builds-android-app-for-website' ) 
        : '';

    
  echo '<div class="updated notice is-dismissible"><p>' . 
    sprintf( 
        // Translators: 1: App name, 2: App readiness message, 3: Customization URL.
        esc_html__( 
            'Thank you for installing <strong>%1$s</strong> %2$s <a href="%3$s">Customize your app &rarr;</a><br/>Note: Manifest and service worker will be generated upon the first visit to the respective sub-site admin.', 
            'wapppress-builds-android-app-for-website' 
        ),
        esc_html( INSTANTAPPY_NAME ), // Corrected app name reference
        esc_html( $INSTANTAPPY_is_ready ), // Ensured safe output
        esc_url( admin_url( 'admin.php?page=INSTANTAPPY' ) ) // Escaped URL
    ) . 
    '</p></div>';


    // Delete transient
    delete_transient( 'INSTANTAPPY_network_admin_notice_activation' );
}

	
}
add_action( 'network_admin_notices', 'INSTANTAPPY_network_admin_notices' );

/**
 * PWA Plugin deactivation
 *
  */
function INSTANTAPPY_deactivate_plugin( $network_active ) {
	
	// Delete PWA manifest
	INSTANTAPPY_delete_pwa_manifest();
	
	// Delete PWA service worker
	INSTANTAPPY_delete_sw();
	
	// For multisites handling
	INSTANTAPPY_multisite_activation_status( false );
	
	// Network deactivation
	if ( $network_active === true ) {
		INSTANTAPPY_multisite_network_deactivator();
	}
}
register_deactivation_hook( INSTANTAPPY_PATH . 'instantappy-pwa.php', 'INSTANTAPPY_deactivate_plugin' );

/**
 * 2. functions related to the plugin settings
 *
 */


/**
 * Validate user input
 */
function INSTANTAPPY_user_input_validation( $settings ) {
	
	// Validate PWA Name
	$settings['app_name'] = sanitize_text_field( $settings['app_name'] ) == '' ? get_bloginfo( 'name' ) : sanitize_text_field( $settings['app_name'] );
	
	// Validate PWA Short Name
	$settings['app_short_name'] = sanitize_text_field( $settings['app_short_name'] ) == '' ? get_bloginfo( 'name' ) : sanitize_text_field( $settings['app_short_name'] );
	
	// Validate PWA description
	$settings['description'] = sanitize_text_field( $settings['description'] );
	
	// Validate PWA background_color
	$settings['background_color'] = preg_match( '/#([a-f0-9]{3}){1,2}\b/i', $settings['background_color'] ) ? sanitize_text_field( $settings['background_color'] ) : '#39a1ff';
	
	// Validate PWA theme_color
	$settings['theme_color'] = preg_match( '/#([a-f0-9]{3}){1,2}\b/i', $settings['theme_color'] ) ? sanitize_text_field( $settings['theme_color'] ) : '#39a1ff';
	
	// Validate PWA icon
	$settings['icon'] = ! empty( $settings['icon'] )
		? sanitize_text_field( INSTANTAPPY_pwa_httpsify( $settings['icon'] ) )
		: INSTANTAPPY_pwa_httpsify( INSTANTAPPY_PATH_SRC . 'public/images/logo.png' );

	// Sanitize splash screen icon
	$settings['splash_icon'] = ! empty( $settings['splash_icon'] )
		? sanitize_text_field( INSTANTAPPY_pwa_httpsify( $settings['splash_icon'] ) )
		: '';

	
	return $settings;
}
			
/**
 * Get PWA settings 
 */
function INSTANTAPPY_grab_pwa_basic_settings() {

	$defaults = array(
				'app_name'			=> get_bloginfo( 'name' ),
				'app_short_name'	=> get_bloginfo( 'name' ),
				'description'		=> get_bloginfo( 'description' ),				
				'background_color' 	=> '#39a1ff',
				'theme_color' 		=> '#39a1ff',
				'start_url' 		=> 0,
				'start_url_amp'		=> 0,
				'offline_page' 		=> 0,
				'orientation'		=> 1,
			);

	$settings = get_option( 'INSTANTAPPY_settings', $defaults );
	
	return $settings;
}

/**
 *
 * Regenerate/create manifest
 
 */
function INSTANTAPPY_after_saving_pwa_settings() {
	
	// Regenerate/create manifest
	INSTANTAPPY_generate_pwa_manifest();
	
	// Regenerate/create service worker
	INSTANTAPPY_generate_pwa_sw();
}
add_action( 'add_option_INSTANTAPPY_settings', 'INSTANTAPPY_after_saving_pwa_settings' );
add_action( 'update_option_INSTANTAPPY_settings', 'INSTANTAPPY_after_saving_pwa_settings' );

/**
 * Admin footer Text
 */
function INSTANTAPPY_footer_text( $default ) {
    
	// Retun default on non-plugin pages
	$screen = get_current_screen();
	if ( strpos( $screen->id, 'INSTANTAPPY' ) === false ) {
		return $default;
	}
	
    $INSTANTAPPY_footer_text = '';
	
	return $INSTANTAPPY_footer_text;
}
add_filter( 'admin_footer_text', 'INSTANTAPPY_footer_text' );
/**
 * 3. functions related to UI setup
 *
 */

/**
 * PWA  Application Name
 *
 */
function INSTANTAPPY_app_name_settings() {

	// PWA  Grab Settings
	$settings = INSTANTAPPY_grab_pwa_basic_settings(); ?>
	
	<fieldset>
		
		<input type="text" name="INSTANTAPPY_settings[app_name]" class="regular-text" value="<?php if ( isset( $settings['app_name'] ) && ( ! empty($settings['app_name']) ) ) echo esc_attr($settings['app_name']); ?>"/>
		
	</fieldset>

	<?php
}

/**
 * PWA  Short Name
 *
 */
function INSTANTAPPY_app_short_name_settings() {

	// Grab Settings
	$settings = INSTANTAPPY_grab_pwa_basic_settings(); ?>
	
	<fieldset>
		
		<input type="text" name="INSTANTAPPY_settings[app_short_name]" class="regular-text" value="<?php if ( isset( $settings['app_short_name'] ) && ( ! empty($settings['app_short_name']) ) ) echo esc_attr($settings['app_short_name']); ?>"/>
		
		
		
	</fieldset>

	<?php
}

/**
 * PWA Description
 *
 */
function INSTANTAPPY_description_settings() {

	// Get PWA Settings
	$settings = INSTANTAPPY_grab_pwa_basic_settings(); ?>
	
	<fieldset>
		
		<input type="text" name="INSTANTAPPY_settings[description]" class="regular-text" value="<?php if ( isset( $settings['description'] ) && ( ! empty( $settings['description'] ) ) ) echo esc_attr( $settings['description'] ); ?>"/>
		
		<p class="description">
			<?php esc_html( 'A brief description of what your app is about.', 'wapppress-builds-android-app-for-website' ); ?>
		</p>
		
	</fieldset>

	<?php
}
/**
 * Background Color
 *
 */
function INSTANTAPPY_background_color_settings() {

	// Grab Settings
	$settings = INSTANTAPPY_grab_pwa_basic_settings(); ?>
	
	<!-- Background Color -->
	<input type="color" name="INSTANTAPPY_settings[background_color]" id="INSTANTAPPY_settings[background_color]" class="INSTANTAPPY-colorpicker" value="<?php echo isset( $settings['background_color'] ) ? esc_attr( $settings['background_color']) : '#39a1ff'; ?>" data-default-color="#39a1ff">
	
	

	<?php
}

/**
 * Theme Color
 */
function INSTANTAPPY_theme_color_settings() {

	// Grab Settings
	$settings = INSTANTAPPY_grab_pwa_basic_settings(); ?>
	
	<!-- Theme Color -->
	<input type="color" name="INSTANTAPPY_settings[theme_color]" id="INSTANTAPPY_settings[theme_color]" class="INSTANTAPPY-colorpicker" value="<?php echo isset( $settings['theme_color'] ) ? esc_attr( $settings['theme_color']) : '#39a1ff'; ?>" data-default-color="#39a1ff">
	
	

	<?php
}

/**
 * Default Orientation Dropdown
 *
 */
function INSTANTAPPY_orientation_settings() {

	// Grab Settings
	$settings    = INSTANTAPPY_grab_pwa_basic_settings();
	$orientation = isset( $settings['orientation'] ) ? (int) $settings['orientation'] : 0;
	?>

	<label for="INSTANTAPPY_settings_orientation">
		<select name="INSTANTAPPY_settings[orientation]" id="INSTANTAPPY_settings_orientation">

			<option value="0" <?php selected( $orientation, 0 ); ?>>
				<?php esc_html_e( 'Follow Device Orientation', 'wapppress-builds-android-app-for-website' ); ?>
			</option>

			<option value="1" <?php selected( $orientation, 1 ); ?>>
				<?php esc_html_e( 'Portrait', 'wapppress-builds-android-app-for-website' ); ?>
			</option>

			<option value="2" <?php selected( $orientation, 2 ); ?>>
				<?php esc_html_e( 'Landscape', 'wapppress-builds-android-app-for-website' ); ?>
			</option>

		</select>
	</label>

	<?php
}


/**
 * PWA Manifest Status
 */
function INSTANTAPPY_manifest_status_settings() {
	
	/** 
	 * Check to see if the manifest exists, if not generate.
	 */
	if ( INSTANTAPPY_get_contents( INSTANTAPPY_manifest( 'abs' ) ) || INSTANTAPPY_generate_pwa_manifest() ) {
			printf(
				'<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> %s</p>',
				esc_html__( 'Manifest.', 'wapppress-builds-android-app-for-website' ) // Escaping translation output
			);

	} else {
		
				printf(
			'<p><span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span> %s</p>',
			sprintf(
				esc_html( 'Manifest generation failed. Check if WordPress can write to your root folder (the same folder with wp-config.php). <a href="%s" target="_blank">Read more &rarr;</a>', 'wapppress-builds-android-app-for-website' ),
				esc_url( 'YOUR_READ_MORE_LINK' ) // Replace with actual link
			)
		);

	}
}

/**
 * PWA Service Worker Status
 *
 */
function INSTANTAPPY_PWA_service_worker_status_settings() {

	// See INSTANTAPPY_manifest_status_settings() for documentation.
	if ( INSTANTAPPY_get_contents( INSTANTAPPY_PWA_service_worker( 'abs' ) ) || INSTANTAPPY_generate_pwa_sw() ) {
		
					printf(
				'<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> %s</p>',
				esc_html__( 'Service worker', 'wapppress-builds-android-app-for-website' )
			);

	} else {
		
				printf(
			'<p><span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span> %s</p>',
			esc_html__( 'Service worker generation failed. Check if WordPress can write to your root folder (the same folder with wp-config.php).', 'wapppress-builds-android-app-for-website' )
		);

	}
}

/**
 * PWA HTTPS Status
 *
 */
function INSTANTAPPY_https_status_settings() {

	if ( is_ssl() ) {
		
		printf(
			'<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> %s</p>',
			esc_html__( 'HTTPS.', 'wapppress-builds-android-app-for-website' )
		);

	} else {
		
					printf(
				'<p><span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span> %s</p>',
				esc_html__( 'For PWA, your website should be over HTTPS.', 'wapppress-builds-android-app-for-website' )
			);

	}
}
 