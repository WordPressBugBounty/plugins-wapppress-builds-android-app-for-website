<?php
/**
 * Plugin Name: WappPress
 * Plugin URI:  https://wapppress.com/plugin
 * Description: Convert any WordPress site into an Android App in just 1 click. Easy-to-use WordPress mobile app plugin.
 * Version:     7.0.5
 * Author:      WappPress Team
 * Author URI:  https://wapppress.com
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'WAPPPRESS_PLUGIN_FILE', __FILE__ );
define( 'WAPPPRESS_PLUGIN_BASENAME', plugin_basename( WAPPPRESS_PLUGIN_FILE ) );
define( 'WAPPPRESS_PLUGIN_DIR', plugin_dir_path( WAPPPRESS_PLUGIN_FILE ) );
define( 'WAPPPRESS_PLUGIN_URL', plugin_dir_url( WAPPPRESS_PLUGIN_FILE ) );
define( 'WAPPPRESS_COMPILE_ID', 'https://wapppress.com' );


class wappPress {

    public static $dirInc;
    public static $dirJs;
    public static $dirCss;
    public static $dirImg;
    public static $dirInsPWA;

    public function __construct() {

        self::$dirInc 		 = WAPPPRESS_PLUGIN_DIR . 'includes/';
        self::$dirCss		 = WAPPPRESS_PLUGIN_URL . 'css/';
        self::$dirJs 		 = WAPPPRESS_PLUGIN_URL . 'js/';
        self::$dirImg 		 = WAPPPRESS_PLUGIN_URL . 'images/';
		///////////
		/** * Instantappy PWA – load once */
		add_action( 'plugins_loaded', array( $this, 'load_instantappy_pwa' ), 5 );

	
        // Load plugin
        add_action( 'plugins_loaded', array( $this, 'load_plugin' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_custom_scripts' ) );
		///////
		//add_action( 'plugins_loaded', array( $this, 'load_instantappy_pwa_plugin' ) );
        // Include required files
        require_once self::$dirInc . 'wappPress_admin_setting.php';
        require_once self::$dirInc . 'wappPress_customize.php';
        new WappPress_customize();

        // Get plugin options
        $options = get_option( 'wapppress_settings' );

        // Hide/Show App Name
        if ( ! empty( $options['wapppress_name'] ) ) {
            add_filter( 'bloginfo', array( $this, 'alter_blog_name' ), 10, 2 );
        }

        // Hide/Show Post Author
        if ( empty( $options['wapppress_theme_author'] ) || $options['wapppress_theme_author'] !== 'on' ) {
            add_action( 'loop_start', array( $this, 'remove_post_author' ) );
        }

        // Hide/Show Post Date
        if ( empty( $options['wapppress_theme_date'] ) || $options['wapppress_theme_date'] !== 'on' ) {
            add_action( 'loop_start', array( $this, 'remove_post_date' ) );
        }

        // Disable Comments if needed
        if ( empty( $options['wapppress_theme_comment'] ) || $options['wapppress_theme_comment'] !== 'on' ) {
            $this->disable_comments();
        }
    }
	public function load_instantappy_pwa() {

		$instantappy_dir = WAPPPRESS_PLUGIN_DIR . 'instantappy-pwa/';

		// Core (always)
		require_once $instantappy_dir . 'instantappy-pwa.php';
		require_once $instantappy_dir . 'includes/instantappy-config-and-functions.php';
		require_once $instantappy_dir . 'includes/instantappy-common-functions.php';
		require_once $instantappy_dir . 'public/public-manifest-sw-functions.php';

		// Admin only
		if ( is_admin() ) {
			require_once $instantappy_dir . 'includes/instantappy-pwa-admin-setting.php';

			// Ensure class is instantiated only once
			if ( class_exists( 'instantappy_pwa_admin_setting' ) ) {
				new instantappy_pwa_admin_setting();
			}
		}
	}

    public function load_plugin() {
        require_once self::$dirInc . 'wappPress_theme_switcher.php';
        new WappPress_theme_switcher();
    }
	//////Instantappy PWA///////
	public function load_instantappy_pwa_plugin() {
	
	
     }
   public function admin_custom_scripts() {
    // Use plugin version or filemtime for cache busting
    $plugin_version = defined( 'WAPPPRESS_VERSION' ) ? WAPPPRESS_VERSION : time();

    // Enqueue CSS
    wp_enqueue_style(
    'wapppress-bootstrap',
    self::$dirCss . 'bootstrap.min.css',
    array(),
	 file_exists( self::$dirCss . 'bootstrap.min.css' ) ? filemtime( self::$dirCss . 'bootstrap.min.css' ) : false
	);
    wp_enqueue_style(
        'wapppress-admin-style',
        self::$dirCss . 'styles-admin.css',
        array(),
    	 file_exists( self::$dirCss . 'styles-admin.css' ) ? filemtime( self::$dirCss . 'styles-admin.css' ) : false
    );
    wp_enqueue_style(
        'wapppress-wp-admin',
        self::$dirCss . 'wp-admin-wapp-style.css',
        array(),
		 file_exists( self::$dirCss . 'wp-admin-wapp-style.css' ) ? filemtime( self::$dirCss . 'wp-admin-wapp-style.css' ) : false
    );
    wp_enqueue_style(
        'wapppress-media',
        self::$dirCss . 'media-queries.css',
        array(),
		 file_exists( self::$dirCss . 'media-queries.css' ) ? filemtime( self::$dirCss . 'media-queries.css' ) : false
    );

    // Enqueue JS
    wp_enqueue_script(
        'wapppress-bootstrap',
        self::$dirJs . 'bootstrap.bundle.min.js',
        array( 'jquery' ),
     	file_exists( self::$dirJs . 'bootstrap.bundle.min.js' ) ? filemtime( self::$dirJs . 'bootstrap.bundle.min.js' ) :  true,
		false // Load in footer
    );
    wp_enqueue_script(
        'wapppress-validate',
        self::$dirJs . 'jquery.validate.js',
        array( 'jquery' ),
      	file_exists( self::$dirJs . 'jquery.validate.js' ) ? filemtime( self::$dirJs . 'jquery.validate.js' ) :  true,
		false // Load in footer
    );
    wp_enqueue_script(
        'wapppress-additional',
        self::$dirJs . 'additional-methods.min.js',
        array( 'jquery' ),
		file_exists( self::$dirJs . 'additional-methods.min.js' ) ? filemtime( self::$dirJs . 'additional-methods.min.js' ) :  true,
		false // Load in footer
    );
	wp_enqueue_script(
        'wapppress-jquery.loader.min',
        self::$dirJs . 'jquery.loader.min.js',
        array( 'jquery' ),
		file_exists( self::$dirJs . 'jquery.loader.min.js' ) ? filemtime( self::$dirJs . 'jquery.loader.min.js' ) :  true,
		false // Load in footer
    );

    // Admin custom JS
    wp_enqueue_script(
        'wapppress-custom-js',
        self::$dirJs . 'admin-script.min.js',
        array( 'jquery' ),
       	file_exists( self::$dirJs . 'admin-script.min.js' ) ? filemtime( self::$dirJs . 'admin-script.min.js' ) :  true,
		false // Load in footer
    );

    // Pass data to JS
    wp_localize_script(
        'wapppress-custom-js',
        'wapppressPluginData',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'wapppress_nonce' ),
        )
    );
}


    public function alter_blog_name( $output, $show ) {
        if ( $show === 'name' ) {
            $options = get_option( 'wapppress_settings' );
            return ! empty( $options['wapppress_name'] ) ? sanitize_text_field( $options['wapppress_name'] ) : $output;
        }
        return $output;
    }

    public function remove_post_author() {
        add_filter( 'comment_author', '__return_false' );
    }

    public function remove_post_date() {
        $filters = array(
            'the_date', 'the_time', 'the_modified_date',
            'get_the_date', 'get_the_time', 'get_the_modified_date'
        );
        foreach ( $filters as $filter ) {
            add_filter( $filter, '__return_false' );
        }
    }

    private function disable_comments() {
        add_filter( 'get_comments_number', array( $this, 'custom_comment_count' ) );
        add_action( 'admin_init', array( $this, 'disable_post_type_comments' ) );
        add_filter( 'comments_open', '__return_false', 20, 2 );
        add_filter( 'pings_open', '__return_false', 20, 2 );
        add_filter( 'comments_array', '__return_empty_array', 10, 2 );
    }

 public function custom_comment_count( $post_id ) {
    $post_id = absint( $post_id );
    if ( ! $post_id ) {
        global $id;
        $post_id = absint( $id );
    }

    $post = get_post( $post_id );
    if ( ! $post ) {
        return 0;
    }

    $post_owner  = absint( $post->post_author );
    $owner_email = sanitize_email( get_the_author_meta( 'user_email', $post_owner ) );

    // Cache results
    $cache_key = 'custom_comment_count_' . $post_id;
    $cached    = wp_cache_get( $cache_key, 'custom_comment_count' );

    if ( false !== $cached ) {
        return (int) $cached;
    }

    // Query all approved comments excluding owner/email
    $args = array(
        'status'   => 'approve',
        'count'    => true,
        'user_id__not_in'     => array( $post_owner ),
        'author_email__not_in'=> array( $owner_email ),
        // No post__not_in here
    );

    $comments_query = new WP_Comment_Query();
    $total_comments = (int) $comments_query->query( $args );

    // Get count of current post’s comments separately
    $post_comments = get_comments( array(
        'status'  => 'approve',
        'count'   => true,
        'post_id' => $post_id,
    ) );

    // Subtract current post’s comments from total
    $count = max( 0, $total_comments - (int) $post_comments );

    wp_cache_set( $cache_key, $count, 'custom_comment_count', 600 );

    return $count;
}




    public function disable_post_type_comments() {
        $post_types = get_post_types();
        foreach ( $post_types as $post_type ) {
            if ( post_type_supports( $post_type, 'comments' ) ) {
                remove_post_type_support( $post_type, 'comments' );
                remove_post_type_support( $post_type, 'trackbacks' );
            }
        }
    }
}

new wappPress();
