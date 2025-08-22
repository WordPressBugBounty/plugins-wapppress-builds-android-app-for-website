<?php
/*
Plugin Name: WappPress-Basic
Plugin URI: http://wapppress.com
Description: A WordPress mobile app plugin that makes it easy to build android apps for WordPress website. WappPress converts any WordPress site into Android App in just 1 click and 1 minute
Version: 6.0.9
Author: WappPress Team
Author URI: http://wapppress.com
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
*/
 class wappPress {
	public static $dirPath;
	public static $dirUrl;
	public static $dirInc;
	public static $dirJs;
	public static $dirCss;
	public static $dirImg;
	function __construct() {
		
	  // Define plugin constants
		$dirPath  = trailingslashit( plugin_dir_path( __FILE__ ) );
		$dirUrl   = trailingslashit( plugins_url( dirname( plugin_basename( __FILE__ ) ) ) );
		$dirInc   = $dirPath  . 'includes/';
		$dirCss   = $dirUrl  . 'css/';
		$dirImg   = $dirUrl  . 'images/';
		$dirJs    = $dirUrl  . 'js/';
		
		
		// Setup our plugin loaded
		add_action( 'plugins_loaded', array( $this, 'load_plugin' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_custom_scripts' ) );
		
		
		//Include file	
		require_once( $dirInc . 'wappPress_admin_setting.php' );
		require_once( $dirInc . 'wappPress_customize.php' );
		new wappPress_customize();
		$options = get_option('wapppress_settings');
		define('COMPILE_ID','http://wapppress.com');
		//Hide/Show App Name
		if(!empty($options['wapppress_name'])){	
			add_filter( 'bloginfo', 'wpse_alter_blog_name', 10, 2 );
			function wpse_alter_blog_name( $output, $show ) {
			$options = get_option('wapppress_settings');
			$title= $options['wapppress_name'];
			if ( $show != 'name' ) return $output;
				return $title;
			}
		}
				
		//Hide/Show Post Author Name
		if(@$options['wapppress_theme_author']!='on'){
			function wapppress_remove_post_auther() {
				add_filter('comment_author', '__return_false');
			}
		 add_action('loop_start', 'wapppress_remove_post_auther'); 
	  }	
	  
	  //Hide/Show Post Date 
		if(@$options['wapppress_theme_date']!='on'){
			function wappPress_remove_post_date() {
				add_filter('the_date', '__return_false');
				add_filter('the_time', '__return_false');
				add_filter('the_modified_date', '__return_false');
				add_filter('get_the_date', '__return_false');
				add_filter('get_the_time', '__return_false');
				add_filter('get_the_modified_date', '__return_false');
			} 
			add_action('loop_start', 'wappPress_remove_post_date');
		}	
		
	  
		if(@$options['wapppress_theme_comment'] != 'on' ){
					//Hide Comment Count
					add_filter('get_comments_number','wapp_post_comment_count');
					function wapp_post_comment_count($post_id) {
						global $id;
						$post_id = (int) $post_id;
						if ( !$post_id ) {
							$post_id = (int) $id;
						}
						$post = get_post($post_id);
						$post_owner = $post->post_author;
						$owner_email = get_the_author_meta('user_email', $post_owner);

						$args = array(
							'post__not_in' => array($id),
							'status' => 'approve',
							'user_id__not_in' => array($post_owner),
							'author_email__not_in' => array($owner_email),
							'count' => true
						);

						$comments_query = new WP_Comment_Query($args);
						$newcount = $comments_query->query($args);
						return $newcount;

					}
					
				// Disable support for comments and trackbacks in post types
					function df_disable_comments_post_types_support() {
						$post_types = get_post_types();
						foreach ($post_types as $post_type) {
							if(post_type_supports($post_type, 'comments')) {
								remove_post_type_support($post_type, 'comments');
								remove_post_type_support($post_type, 'trackbacks');
							}
						}
					}
					add_action('admin_init', 'df_disable_comments_post_types_support');
					// Close comments on the front-end
					function df_disable_comments_status() {
						return false;
					}
					add_filter('comments_open', 'df_disable_comments_status', 20, 2);
					add_filter('pings_open', 'df_disable_comments_status', 20, 2);

					// Hide existing comments
					function df_disable_comments_hide_existing_comments($comments) {
						$comments = array();
						return $comments;
					}
					add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);
				
	      } 
	}
	function load_plugin() {
		$dirPath1  = trailingslashit( plugin_dir_path( __FILE__ ) );
		$dirInc1     = $dirPath1  . 'includes/';
		require_once( $dirInc1 . 'wappPress_theme_switcher.php' );
		new wappPress_theme_switcher();
	}
	function admin_custom_scripts(){ 
		//Plugins css files
		wp_enqueue_style( 'styles-admin', plugins_url( 'css/styles-admin.css',  __FILE__ ) );
		wp_enqueue_style( 'jquery.loader.min', plugins_url( 'css/jquery.loader.min.css',  __FILE__ ) );
		wp_enqueue_style( 'wp-admin-wapp-style', plugins_url( 'css/wp-admin-wapp-style.css',  __FILE__ ) );
		wp_enqueue_style( 'wp-slider', plugins_url( 'css/wp-slider.css',  __FILE__ ) );
		wp_enqueue_style( 'media-queries', plugins_url( 'css/media-queries.css',  __FILE__ ) );
		wp_enqueue_style( 'jquery.Jcrop.min', plugins_url( 'css/jquery.Jcrop.min.css',  __FILE__ ) );
		
		//Plugins js files		
		wp_enqueue_script( 'jquery.loader.min', plugins_url( 'js/jquery.loader.min.js',  __FILE__ ), array('jquery'));
		wp_enqueue_script( 'wp-slides.min.jquery', plugins_url( 'js/wp-slides.min.jquery.js',  __FILE__ ), array('jquery'));
		wp_enqueue_script( 'wp-selector', plugins_url( 'js/wp-selector.js',  __FILE__ ), array('jquery'));
		wp_enqueue_script( 'jquery.validate', plugins_url( 'js/jquery.validate.js',  __FILE__ ), array('jquery'));
		wp_enqueue_script( 'additional-methods.min', plugins_url( 'js/additional-methods.min.js',  __FILE__ ), array('jquery'));
			wp_enqueue_script( 'jquery.Jcrop.min', plugins_url( 'js/jquery.Jcrop.min.js',  __FILE__ ), array('jquery'));
		// Enqueue custom script		
		wp_enqueue_script(
			'wapppress-custom-js',
			plugin_dir_url(__FILE__) . 'js/admin-script.min.js',
			array('jquery'),
			filemtime(plugin_dir_path(__FILE__) . 'js/admin-script.min.js'),
			true
		);

		 // Pass data to JavaScript
		wp_localize_script('wapppress-custom-js', 'wapppressPluginData', array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('wapppress_nonce'),
		));

	}
}
new wappPress();
