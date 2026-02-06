<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class instantappy_pwa_admin_setting extends instantappy_pwa {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'status_settings' ) );
	}

	/* ---------------------------------------------------
	 * REGISTER SETTINGS
	 * --------------------------------------------------- */
	public function register_settings() {

		register_setting(
			'INSTANTAPPY_settings_group',
			'INSTANTAPPY_settings',
			'INSTANTAPPY_user_input_validation'
		);

		add_settings_section(
			'INSTANTAPPY_settings_group',
			'',
			'__return_false',
			'INSTANTAPPY_settings_group'
		);

		add_settings_field(
			'INSTANTAPPY_app_name',
			__( 'PWA Name', 'wapppress-builds-android-app-for-website' ),
			'INSTANTAPPY_app_name_settings',
			'INSTANTAPPY_settings_group',
			'INSTANTAPPY_settings_group'
		);

		add_settings_field(
			'INSTANTAPPY_app_short_name',
			__( 'PWA Short Name', 'wapppress-builds-android-app-for-website' ),
			'INSTANTAPPY_app_short_name_settings',
			'INSTANTAPPY_settings_group',
			'INSTANTAPPY_settings_group'
		);

		add_settings_field(
			'INSTANTAPPY_description',
			__( 'PWA Description', 'wapppress-builds-android-app-for-website' ),
			'INSTANTAPPY_description_settings',
			'INSTANTAPPY_settings_group',
			'INSTANTAPPY_settings_group'
		);

		add_settings_field(
			'INSTANTAPPY_background_color',
			__( 'PWA Background Color', 'wapppress-builds-android-app-for-website' ),
			'INSTANTAPPY_background_color_settings',
			'INSTANTAPPY_settings_group',
			'INSTANTAPPY_settings_group'
		);

		add_settings_field(
			'INSTANTAPPY_theme_color',
			__( 'PWA Theme Color', 'wapppress-builds-android-app-for-website' ),
			'INSTANTAPPY_theme_color_settings',
			'INSTANTAPPY_settings_group',
			'INSTANTAPPY_settings_group'
		);


		add_settings_field(
			'INSTANTAPPY_orientation',
			__( 'PWA Orientation', 'wapppress-builds-android-app-for-website' ),
			'INSTANTAPPY_orientation_settings',
			'INSTANTAPPY_settings_group',
			'INSTANTAPPY_settings_group'
		);
	}
	/* ---------------------------------------------------
	 * STATUS SETTINGS
	 * --------------------------------------------------- */
	public function status_settings() {

		add_settings_section(
			'INSTANTAPPY_status_group',
			__( 'PWA Status', 'wapppress-builds-android-app-for-website' ),
			'__return_false',
			'INSTANTAPPY_status_group'
		);

		add_settings_field(
			'INSTANTAPPY_manifest_status',
			__( 'Manifest', 'wapppress-builds-android-app-for-website' ),
			'INSTANTAPPY_manifest_status_settings',
			'INSTANTAPPY_status_group',
			'INSTANTAPPY_status_group'
		);

		add_settings_field(
			'INSTANTAPPY_PWA_service_worker_status',
			__( 'Service Worker', 'wapppress-builds-android-app-for-website' ),
			'INSTANTAPPY_PWA_service_worker_status_settings',
			'INSTANTAPPY_status_group',
			'INSTANTAPPY_status_group'
		);

		add_settings_field(
			'INSTANTAPPY_https_status',
			__( 'HTTPS', 'wapppress-builds-android-app-for-website' ),
			'INSTANTAPPY_https_status_settings',
			'INSTANTAPPY_status_group',
			'INSTANTAPPY_status_group'
		);
	}

	
	/* ---------------------------------------------------
	 * SETTINGS PAGE OUTPUT
	 * --------------------------------------------------- */
	public function instantappy_pwa_settings() {
		// Capability check
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	/************************************
	 * ICON UPLOAD + RESIZE HANDLER
	 ************************************/
	if (
	isset( $_POST['instantappy_icon_nonce'] ) &&
	wp_verify_nonce(
		sanitize_text_field( wp_unslash( $_POST['instantappy_icon_nonce'] ) ),
		'instantappy_icon_action'
	) &&
	isset( $_FILES['app_logo'] ) &&
	! empty( $_FILES['app_logo']['name'] )
	) {

		// Load WP upload helpers
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Validate file array structure
		if (
			! isset( $_FILES['app_logo'] ) ||
			! is_array( $_FILES['app_logo'] ) ||
			empty( $_FILES['app_logo']['name'] ) ||
			! isset(
				$_FILES['app_logo']['type'],
				$_FILES['app_logo']['tmp_name'],
				$_FILES['app_logo']['error'],
				$_FILES['app_logo']['size']
			)
		) {
			add_settings_error(
				'INSTANTAPPY_icon_group',
				'upload_error',
				__( 'Invalid file upload.', 'wapppress-builds-android-app-for-website' ),
				'error'
			);
			return;
		}

		// Build upload array (DO NOT sanitize tmp_name or type)
		$uploaded_file = array(
			'name' => sanitize_file_name( wp_unslash( $_FILES['app_logo']['name'] ) ),

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- MIME type is validated by wp_handle_upload()
			'type' => $_FILES['app_logo']['type'],

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- tmp_name is a server-generated file path
			'tmp_name' => $_FILES['app_logo']['tmp_name'],

			'error' => absint( $_FILES['app_logo']['error'] ),
			'size'  => absint( $_FILES['app_logo']['size'] ),
		);


		// Handle upload
		$upload_overrides = array( 'test_form' => false );
		$movefile         = wp_handle_upload( $uploaded_file, $upload_overrides );

		if ( isset( $movefile['error'] ) ) {
			add_settings_error(
				'INSTANTAPPY_icon_group',
				'upload_error',
				esc_html( $movefile['error'] ),
				'error'
			);
			return;
		}

		// Uploaded file path
		$source_image = $movefile['file'];

		if ( ! file_exists( $source_image ) ) {
			add_settings_error(
				'INSTANTAPPY_icon_group',
				'file_missing',
				__( 'Uploaded image not found.', 'wapppress-builds-android-app-for-website' ),
				'error'
			);
			return;
		}

		// Plugin paths
		$plugin_path = plugin_dir_path( __FILE__ );
		$plugin_path = str_replace( 'includes/', '', $plugin_path );
		$dest_dir    = $plugin_path . 'public/images/';

		wp_mkdir_p( $dest_dir );

		// Resize icons
		require_once __DIR__ . '/instantappy_resize_class.php';

		$sizes = array( 16, 32, 48, 70, 72, 96, 144, 150, 180, 192, 310, 512 );

		foreach ( $sizes as $size ) {
			try {
				$resize = new instantappy_resize( $source_image );
				$resize->resizeImage( $size, $size, 'exact' );
				$resize->saveImage( $dest_dir . "{$size}x{$size}.png", 100 );
			} catch ( Exception $e ) {
				add_settings_error(
					'INSTANTAPPY_icon_group',
					'resize_error',
					__( 'One or more icons could not be generated.', 'wapppress-builds-android-app-for-website' ),
					'error'
				);
				return;
			}
		}
		update_option( 'instantappy_pwa_manifest_version', time() );
		add_settings_error(
			'INSTANTAPPY_icon_group',
			'icon_saved',
			__( 'PWA icons generated successfully.', 'wapppress-builds-android-app-for-website' ),
			'updated'
		);

	}

	/************************************
	 * SETTINGS UPDATED NOTICE
	 ************************************/
	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error(
			'INSTANTAPPY_settings_group',
			'settings_saved',
			__( 'Settings saved successfully.', 'wapppress-builds-android-app-for-website' ),
			'updated'
		);
	}

	settings_errors();

	/************************************
	 * UI
	 ************************************/
	?>
	<script>
	function clickactiontabmenu(secids,secids2,secids3,secids4,secids5,secids6)
	{
		jQuery("#"+secids).show();
		jQuery("#"+secids2).hide();
		jQuery("#"+secids3).hide();
	}
	</script>
	<div class="wrap-pwa">	
	<h1><img src="<?php echo esc_html(plugins_url( '../webroot/images/instantappy_pwa_view-large.png',  __FILE__ )); ?>" title="" alt=""/></h1>
<button type="button" class="btn btn-info" data-target="#settings-sec" onClick="clickactiontabmenu('settings-sec','status-sec','icon-sec')">PWA  Settings</button>
		<button type="button" class="btn btn-info" data-target="#icon-sec" onClick="clickactiontabmenu('icon-sec','status-sec','settings-sec')
		">PWA ICON Settings</button>		
		<button type="button" class="btn btn-info" data-target="#status-sec" onClick="clickactiontabmenu('status-sec',
		'settings-sec','icon-sec')" >PWA  Status</button>

		<form action="options.php" method="post">
			<div id="settings-sec" class="collapse in"  style="display:block" >
		<?php
			settings_fields( 'INSTANTAPPY_settings_group' );
			do_settings_sections( 'INSTANTAPPY_settings_group' );
			submit_button();
			?>
		</div>
		<div id="status-sec" class="collapse" style="display:none">
			<?php
			// Status
			do_settings_sections( 'INSTANTAPPY_status_group' );	// Page slug				
			?>
			</div>
		</form>

		<hr>

		<form method="post" enctype="multipart/form-data">
		<div id="icon-sec" class="collapse in" style="display:none">
			<h2>PWA ICON</h2>

			<input type="file" name="app_logo" required>
			<?php wp_nonce_field( 'instantappy_icon_action', 'instantappy_icon_nonce' ); ?>
			<?php submit_button( __( 'Upload & Generate Icons', 'wapppress-builds-android-app-for-website' ) ); ?>
			</div>
		</form>
	</div>
	<?php
	}
}

new instantappy_pwa_admin_setting();
