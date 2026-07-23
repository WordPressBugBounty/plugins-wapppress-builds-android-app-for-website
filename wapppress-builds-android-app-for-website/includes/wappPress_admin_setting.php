<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class wappPress_admin_setting extends wappPress {
 protected $instantappy_pwa_admin;
	function __construct() {

			add_action( 'admin_menu', array( $this, 'maker_menu' ), 7);

			add_action( 'admin_init', array( $this, 'register_settings' ) );

			add_action( 'wp_ajax_create_app', array( $this, 'create_app' ) );

			add_action( 'wp_ajax_create_push_app', array( $this, 'create_push_app' ) );

			add_action( 'wp_ajax_get_app', array( $this, 'get_app' ) );
		
			//////////////////////////////////////////////////////
			add_action( 'wp_ajax_search_post_handler', array( $this, 'search_post_results' ) );
			add_filter( 'plugin_action_links_' . WAPPPRESS_PLUGIN_BASENAME, array( $this, 'wappPress_insert_action_links' ) );
			add_filter( 'plugin_row_meta', array( $this, 'wappPress_plugin_row_meta' ), 10, 2 );
			add_action( 'admin_notices', array( $this, 'wappPress_trial_expired_notice' ) );
			add_action( 'wp_ajax_wapppress_check_trial', array( $this, 'wapppress_check_trial' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'wapppress_enqueue_admin_assets' ) );
			////////////////////////////////////////////////////
			add_action('init', function () {
				$data = get_option('wapppress_last_build');
				if ( is_array($data) && time() - (int)$data['created_at'] > 72 * HOUR_IN_SECONDS ) {
					delete_option('wapppress_last_build');
				}
			});

			////////////////////////////////////////////////////
			
		$options = get_option('wapppress_settings');
			//Custom Post New
		if(@$options['wapppress_push_post']=='on'){			
			add_action( 'publish_post', [ $this, 'send_push_on_new_post' ], 10, 3 );
			}		
	    if(@$options['wapppress_push_post_edit']=='on'){			
			add_action( 'publish_post', [ $this, 'send_push_on_new_post' ], 10, 3 );
			}
		if(@$options['wapppress_push_product']=='on'){			
			add_action( 'transition_post_status',[ $this, 'send_push_on_product' ], 10, 3 );
			}		
	    if(@$options['wapppress_push_product_edit']=='on'){			
			add_action( 'transition_post_status',[ $this, 'send_push_on_product' ], 10, 3 );
			}			


			
	}

public function register_pwa_menu() {

        $subPushMenu = add_submenu_page(
            $maPlgin,
            __( 'Instantappy PWA', 'wapppress-builds-android-app-for-website' ),
            __( 'Instantappy PWA', 'wapppress-builds-android-app-for-website' ),
            'manage_options',
            $maPWA,
            array( $this->instantappy_pwa_admin, 'instantappy_pwa_settings' )
        );
    }
function wapppress_enqueue_admin_assets() {

    wp_localize_script(
        'wapppress-custom-js',
        'wapppressAjax',
        array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'wapppress_nonce' ),
        )
    );
}


	public function maker_menu() {

	$pageTitle 		= __( 'WappPress', 'wapppress-builds-android-app-for-website' );
	$maPlgin  		= 'wapppressplugin';
	$maPlginBasic  	= 'wapppress-basic';
	$whyPro 		= 'wapppress-why-pro';	
	$maSett 		= 'wapppresssettings';
	$advSett		= 'advancesettings';
	$maPush 		= 'wapppresspush';
	$maPro  		= 'wapppresspro';
	$maFaq  		= 'wapppress-faq';
	$maPWA  		= 'instantappy-pwa';
	$dirPlgUrl  	= trailingslashit( esc_url(plugins_url('wapppress-builds-android-app-for-website')) );
	$plgIcon  		= $dirPlgUrl  . 'images/view.png';

	$dirInc1  		= $dirPlgUrl  . 'includes/';

	$license 		= get_option( 'wapppress_license', '' );
	$proactive=false;
	if ( empty( $license ) || ! preg_match(
		'/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i',
		$license
	) ) {

		add_menu_page(
			$pageTitle,
			__( 'WappPress BASIC', 'wapppress-builds-android-app-for-website' ),
			'manage_options',
			$maPlgin,
			array( $this, 'maker_settings_page' ),
			$plgIcon
		);

	} else {

		add_menu_page(
			$pageTitle,
			__( 'WappPress', 'wapppress-builds-android-app-for-website' ),
			'manage_options',
			$maPlgin,
			array( $this, 'maker_settings_page' ),
			$plgIcon
		);
		$proactive=true;
	}

	// Submenus
	add_submenu_page(
		$maPlgin,
		__( 'Build Android App', 'wapppress-builds-android-app-for-website' ),
		__( 'Build Android App', 'wapppress-builds-android-app-for-website' ),
		'manage_options',
		$maSett,
		array( $this, 'maker_settings_page' )
	);
	/*add_submenu_page(
		$maPlgin,
		__( 'Build App', 'wapppress-builds-android-app-for-website' ),
		__( 'Build App', 'wapppress-builds-android-app-for-website' ),
		'manage_options',
		$maSett,
		array( $this, 'maker_settings_page' )
	);*/
	add_submenu_page(
		$maPlgin,
		__( 'Advance Settings', 'wapppress-builds-android-app-for-website' ),
		__( 'Advance Settings', 'wapppress-builds-android-app-for-website' ),
		'manage_options',
		$advSett,
		array( $this, 'advance_settings_page' )
	);
	

	add_submenu_page(
		$maPlgin,
		__( 'Push Notification', 'wapppress-builds-android-app-for-website' ),
		__( 'Push Notification', 'wapppress-builds-android-app-for-website' ),
		'manage_options',
		$maPush,
		array( $this, 'maker_push_page' )
	);

	add_submenu_page(
		$maPlgin,
		__( 'Activate Pro', 'wapppress-builds-android-app-for-website' ),
		__( 'Activate Pro', 'wapppress-builds-android-app-for-website' ),
		'manage_options',
		$maPro,
		array( $this, 'wapppress_pro_settings' )
	);
	
	add_submenu_page(
		$maPlgin,
		__( 'Build PWA App', 'wapppress-builds-android-app-for-website' ),
		__( 'Build PWA App', 'wapppress-builds-android-app-for-website' ),
		'manage_options',
		$maPWA,
		array( new instantappy_pwa_admin_setting(), 'instantappy_pwa_settings' )
	);
	// Hide it from left menu
	/*add_action( 'admin_head', function () {
		echo '<style>
			#toplevel_page_wapppressplugin .wp-submenu a[href$="page=wapppresssettings"] {
				display:none !important;
			}
		</style>';
	});*/
	add_action( 'admin_head', function () {
		echo '<style>
			#toplevel_page_wapppressplugin .wp-submenu a[href$="page=advancesettings"] {
				display:none !important;
			}
		</style>';
	});
	add_action( 'admin_head', function () {
		echo '<style>
			#toplevel_page_wapppressplugin .wp-submenu a[href$="page=wapppresspush"] {
				display:none !important;
			}
		</style>';
	});
	add_action( 'admin_head', function () {
		echo '<style>
			#toplevel_page_wapppressplugin .wp-submenu a[href$="page=wapppresspro"] {
				display:none !important;
			}
		</style>';
	});
	add_submenu_page(
		$maPlgin,
		__( 'FAQ', 'wapppress-builds-android-app-for-website' ),
		__( 'FAQ', 'wapppress-builds-android-app-for-website' ),
		'manage_options',
		$maFaq,
		array( $this, 'wapppress_faq' )
	);
	if(!$proactive)
	{
		$textWhyPro = "<span style='color: #C84C05;font-weight: 800;font-size: 16px;'>".__( 'Why Pro?', 'wapppress-builds-android-app-for-website' )."</span>";
		add_submenu_page(
		$maPlgin,
		__( 'Why Pro?', 'wapppress-builds-android-app-for-website' ),
		$textWhyPro,
		'manage_options',
		$whyPro,
		array( $this, 'maker_basic_page' ),
		);
		
	}
	
	// Remove duplicate submenu added by WP
	add_action( 'admin_menu', function () use ( $maPlgin ) {
		remove_submenu_page( $maPlgin, $maPlgin );
	}, 999 );
}


	

	//Basic Page 

	public function maker_basic_page(){

	require_once(  'header.php' );

	?>
	
<section class="build_app_section">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="build_app_box">
					<div class="build_app_text1">
						<figure>
							<img src="<?php echo esc_url( plugins_url( '../images/img1.png', __FILE__ ) ); ?>" alt="Build Android App" />
						</figure>
						<p>
							Turn your WordPress website into a <b>Google Play–ready Android App</b><br>
							<span style="font-size:14px;">No coding • Real-time build • One-click setup</span>
						</p>
					</div>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=wapppresssettings' ) ); ?>">
						<button>Build Android App</button>
					</a>
				</div>
				<div style="margin-top:10px; text-align:center">
				<p >
						<b>Why upgrade to Pro?</b><br>
						Build & publish your Android app without limits — ideal for business and production use.
					</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="wapppress_section">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">

				<!-- BASIC VERSION -->
				<div class="wapppress_box1">
					<h3><br>
						WappPress BASIC <br><span>Free</span>  <br><br>
					</h3>

					<ul>
						<li><b>Limited Push Notifications</b></li>
						<li><b>Limited Custom Push Notifications</b></li>
						<li>
							Monetize with Google AdMob Interstitial Ads
							<b>(Limited Time)</b>
						</li>
						<li><b>Android App Validity – 15 Days</b></li>
						<li>Select a different home page for mobile app</li>
						<li>Select different theme for website & mobile app</li>
						<li>Customize launcher icon</li>
						<li>Upload your own app icon</li>
						<li>Customize splash screen</li>
						<li>
							Upload custom splash screen
							<small>( You can upload your own splash screen image, this will be used to capture the user's attention for a short time as a promotion or lead-in)</small>
						</li>
						<li>Ads free (no internal branding)</li>
						<li>Real-time Android app build</li>
						<p>&nbsp;</p>
						<p>&nbsp;</p>
						<p>&nbsp;</p>
						<p>&nbsp;</p>
					</ul>

					<a href="<?php echo esc_url( admin_url( 'admin.php?page=wapppresssettings' ) ); ?>">
						<button>Build App</button>
					</a>
				</div>

				<!-- PRO VERSION -->
				<div class="wapppress_box1">
					<h3>
						<b>WappPress PRO <br/>(Lifetime App Validity) </b><br>
						<span>$24</span> <br>
						<small>One-time payment • No monthly fees</small>
					</h3>

					

					<ul>
						<li><b>Unlimited Push Notifications (No Caps)</b></li>
						<li><b>Advanced Custom Push Notifications</b></li>
						<li>
							Monetize with Google AdMob Interstitial Ads
							<b>(Unlimited)</b>
						</li>
						<li><b>Lifetime Validity of App (No Expiry)</b></li>
						<li>Select a different home page for mobile app</li>
						<li>Select different theme for website & mobile app</li>
						<li>Customize launcher icon</li>
						<li>Upload your own app icon</li>
						<li>Customize splash screen</li>
						<li>
							Upload custom splash screen
							<small>( You can upload your own splash screen image, this will be used to capture the user's attention for a short time as a promotion or lead-in)</small>
						</li>
						<li><b>White-label App (No Branding)</b></li>
						<li><b>Play Store–ready app structure</b></li>
						<li>Real-time Android app build</li>
					</ul>

					<a href="https://goo.gl/bcEb25" target="_blank">
						<button>Upgrade to Pro</button>
					</a>

					<p style="margin-top:8px; font-size:12px;">
						✅ One-time payment &nbsp;|&nbsp;
						✅ No monthly fees &nbsp;|&nbsp;
						✅ Safe WordPress plugin
					</p>
				</div>

			</div>
		</div>
	</div>
</section>

	<section>
			<div style='float:right;display:inline-block;font-family:"open_sansbold";font-size:12px;'>

				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wapppresspro' ) ); ?>"
				   class="submit-build btn btn-info btn-lg"
				   style="background-color:#C84C05; color:#fff; text-decoration:none;">

					Already Have Pro? Click Here
				</a>

			</div>
		</section>

	

	<!---=== Pro PopUp Div  Start ===--->

		<div id="pro_popup">

			<div class="form_upload">

				<span class="close" onclick="close_popup('pro_popup')">x</span>

				<h2 style='text-align:center;'>WappPress Pro version</h2>

					<div style='text-align:center;'>

						<h3><span style='color: #FB9700;display: inline-block;font-family: "open_sansbold";font-size: 12px;'>(FOR JUST &nbsp;<strong style='font-size: 20px;color:#e20202;'>$24</strong> &nbsp; ONLY )</span></h3>

					</div>

					<div style='float:left;display: inline-block;font-family: "open_sansbold";font-size: 12px;'>

						<a  target='_blank' href="javascript:void(0);" ><img src="<?php echo esc_url(plugins_url( '../images/btn2.png',  __FILE__ )) ?>" title="" alt="Proceed To Buy"/></a>

					</div>

			</div>

		</div>	

	<!---=== Pro PopUp Div  End ===--->
		

	

	<?php	

	require_once(  'footer.php' );

	}
		// Advance Setting Page 

public function advance_settings_page()
{
	require_once(  'header.php' );

	

	$dirIncImg  = trailingslashit(plugins_url('wappPress'));

	$options = get_option('wapppress_settings');

	$args= array();	

	$all_themes = wp_get_themes( $args );

	//$check = isset( $options['wapppress_theme_switch'] ) ? esc_attr( $options['wapppress_theme_switch'] ) : '';

	$authorCheck = isset( $options['wapppress_theme_author'] ) ? esc_attr( $options['wapppress_theme_author'] ) : '';

	$dateCheck = isset( $options['wapppress_theme_date'] ) ? esc_attr( $options['wapppress_theme_date'] ) : '';

	$commentCheck = isset( $options['wapppress_theme_comment'] ) ? esc_attr( $options['wapppress_theme_comment'] ) : '';

	$frontpage_id2 =  get_option('page_on_front');
	$pushPostCheck 			= isset( $options['wapppress_push_post'] ) ? esc_attr( $options['wapppress_push_post'] ) : '';
	$pushPostEditCheck 		= isset( $options['wapppress_push_post_edit'] ) ? esc_attr( $options['wapppress_push_post_edit'] ) : '';
	$pushProductCheck 		= isset( $options['wapppress_push_product'] ) ? esc_attr( $options['wapppress_push_product'] ) : '';
	$pushProductEditCheck	= isset( $options['wapppress_push_product_edit'] ) ? esc_attr( $options['wapppress_push_product_edit'] ) : '';
	
	 ?>


	
	<div class="contant-section1">
		
		<div class="section">

		<div class="wrapper">

			<div class="contant-section">
				
				<div class="setting-head">

					<h2>ADVANCE SETTINGS [Optional]</h2>	
				</div>

				

				<!--===Setting Box Start===--->

				<div class="setting-box">
				
					<div class="inner_left">
						<?php settings_errors();?>
						<div class="inner_header2">

							<div class="tabs">

								<div class="tab-content">

								<form method="post" action="options.php">

									<div id="tab1" class="tab active">

										<ul id="toggle-view">

										<?php
										 

											// settings_fields( $option_group )

											settings_fields( 'wapppress_group' );

											// do_settings_sections( $page )

											do_settings_sections( __FILE__ );

											?>

											<li>

											<h3 class="test">Enter Your App name</h3>

											<span><img src="<?php echo esc_url(plugins_url( '../images/arrow.png',  __FILE__ )) ?>" alt=""></span>

											<div class="panel">

												<p>

													<input class="app_input"  type="text" id="wapppress_name" name="wapppress_settings[wapppress_name]" value="<?php echo esc_html(@$options['wapppress_name']); ?>" />

												</p>

											</div>

											</li>

											<!--li>

											<h3>Enable/Disable theme setting on desktop</h3>

											<span><img src="<?php echo esc_url(plugins_url( '../images/arrow.png',  __FILE__ )) ?>" alt=""></span>

											<div class="panel">

												<p>

													<input type="radio" name="wapppress_settings[wapppress_theme_switch]"<?php checked( $check, 'on'.false ); ?> value='on' /> Enable &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" value=''  name="wapppress_settings[wapppress_theme_switch]" <?php checked( $check, ''.false ); ?> /> Disable

												</p>

											</div>

											</li-->

											<li>

											<h3>Select Theme</h3>

											<span><img src="<?php echo esc_url(plugins_url( '../images/arrow.png',  __FILE__ )) ?>" alt=""></span>

											<div class="panel">
											
												<p>

													<select name="wapppress_settings[wapppress_theme_setting]" id="wapppress_theme_setting"  class="app_input_select">

														<?php $the = array(); 

														foreach($all_themes as $theme_val =>$theme_name){ 

														 $nonce = wp_create_nonce('switch-theme_'.$theme_val);

														 $src = esc_url(admin_url().'customize.php?action=preview&theme='.$theme_val);

														 $theme_val = $theme_val == 'option-none' ? '' : esc_attr( $theme_val ); 

														echo  '<option id="'.esc_url($src).'" value="'. esc_html($theme_val) .'" '. selected( @$options['wapppress_theme_setting'],esc_html($theme_val), false) .'>'. esc_html( $theme_name ) .'</option>

														'."\n"; 
														 //echo esc_attr($the[$theme_val]) ;

														} ?>

													</select>

												</p>

											</div>

											</li>

											<li>

											<h3>Use a unique homepage for your app</h3>

											<span><img src="<?php echo esc_url(plugins_url( '../images/arrow.png',  __FILE__ )) ?>" alt=""></span>

											<div class="panel">

												<p>Start typing to search for a page, or enter a page ID.</p>

												<p>

													<?php $frontpage_id1 =  get_option('page_on_front'); 

													if($frontpage_id1 !=@$options['wapppress_home_setting']){

													?>

													<input class="app_input"  type="text" id="wapppress_home_setting" name="wapppress_settings[wapppress_home_setting]" value="<?php echo  esc_html(@$options['wapppress_home_setting']); ?>" />

													<?php }else{ ?>

													<input class="app_input"  type="text" id="wapppress_home_setting" name="wapppress_settings[wapppress_home_setting]" value="" />

													<?php } ?>

												</p>

										<div class='wapppress_field_markup_text' id="wapppress_field_markup_text"></div>

											</div>

											</li>

											<li>

											<h3>Customize Your Theme</h3>

											<span><img src="<?php echo esc_url(plugins_url( '../images/arrow.png',  __FILE__ )) ?>" alt=""></span>

											<div class="panel">

												<p>

													<input  type="checkbox" name="wapppress_settings[wapppress_theme_date]"  class="checkbox"  <?php checked( $dateCheck, 'on'.false ); ?> /> Display Date

												</p>

												<p>

													<input  type="checkbox" name="wapppress_settings[wapppress_theme_comment]"  class="checkbox"  <?php checked($commentCheck, 'on'.false ); ?> />  Display Comments

												</p>

												

											</div>

											</li>
											<li>

											<h3>Custom Push Notificaton Settings</h3>

											<span><img src="<?php echo esc_url(plugins_url( '../images/arrow.png',  __FILE__ )) ?>" alt=""></span>

											<div class="panel">

												<p>

													<input  type="checkbox" name="wapppress_settings[wapppress_push_post]"  class="checkbox"  <?php checked( $pushPostCheck, 'on'.false ); ?> /> Send Push Notification on New Post

												</p>
												<p>

													<input  type="checkbox" name="wapppress_settings[wapppress_push_post_edit]"  class="checkbox"  <?php checked( $pushPostEditCheck, 'on'.false ); ?> /> Send Push Notification on Post Updation

												</p>
												<p>

													<input  type="checkbox" name="wapppress_settings[wapppress_push_product]"  class="checkbox"  <?php checked($pushProductCheck, 'on'.false ); ?> /> Send Push Notification on New Product

												</p>
												<p>

													<input  type="checkbox" name="wapppress_settings[wapppress_push_product_edit]"  class="checkbox"  <?php checked($pushProductEditCheck, 'on'.false ); ?> /> Send Push Notification on Product Updation

												</p>
												
												

											</div>

											</li>

										</ul>

									</div>

				
									<div class="save-btn">
<input id="save_changes" class="submit-build btn btn-info btn-lg" type="submit" onclick="document.getElementById('bulid').scrollIntoView();return false;"  value="Save Changes" name="save_changes">

						
									</div>

									
								</div>


								</form>

								

							</div>

						</div>

					</div>

					<div class="wrap-right mobileFrame">
					
<iframe frameborder="0" allowtransparency="no" name="mobile_frame" id="mobile_frame" src="https://wapppress.com/prw.php?shop_url=<?php echo esc_url(get_site_url()) ; ?>"/>

						</iframe>
					</div>

					

					<div class="clear">

					</div>

				</div>

				<!--===Setting Box End===--->

				

				<!--===Android APP Box Start===--->

			

				<!--===Android APP Box End===--->

				

			</div>
		</div>

	</div>

</div>

<?php require_once( 'footer.php' );

}

	// Setting Page 

	public function maker_settings_page(){

	require_once(  'header.php' );

	

	$dirIncImg  = trailingslashit(esc_url(plugins_url('wapppress-builds-android-app-for-website')));

	$options = get_option('wapppress_settings');

	$args= array();	

	$all_themes = wp_get_themes( $args );

	$check = isset( $options['wapppress_theme_switch'] ) ? esc_attr( $options['wapppress_theme_switch'] ) : '';

	$authorCheck = isset( $options['wapppress_theme_author'] ) ? esc_attr( $options['wapppress_theme_author'] ) : '';

	$dateCheck = isset( $options['wapppress_theme_date'] ) ? esc_attr( $options['wapppress_theme_date'] ) : '';

	$commentCheck = isset( $options['wapppress_theme_comment'] ) ? esc_attr( $options['wapppress_theme_comment'] ) : '';

	$frontpage_id2 =  get_option('page_on_front');
	
	$pushPostCheck 			= isset( $options['wapppress_push_post'] ) ? esc_attr( $options['wapppress_push_post'] ) : '';
	$pushPostEditCheck 		= isset( $options['wapppress_push_post_edit'] ) ? esc_attr( $options['wapppress_push_post_edit'] ) : '';
	$pushProductCheck 		= isset( $options['wapppress_push_product'] ) ? esc_attr( $options['wapppress_push_product'] ) : '';
	$pushProductEditCheck	= isset( $options['wapppress_push_product_edit'] ) ? esc_attr( $options['wapppress_push_product_edit'] ) : '';
	
	
	if(@$options['wapppress_theme_switch'] =='on'){ ?>

	<input type="hidden" id="wapppress_url"  value='<?php echo esc_url(get_site_url()) ; ?>' /> 

	<?php }else{ ?>

	<input type="hidden" id="wapppress_url"  value='<?php echo esc_url(get_site_url()).'/?wapppress=1' ; ?>' /> 

	<?php } ?>
<?php	$license = get_option('wapppress_license', '');  
 if(empty($license)&&(! preg_match( '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i',  $license))) 
	{		?>
	<div class="Section1">
    <div class="container-fluid" style="max-width:1200px;">
        <div class="row">
            <div class="col-12">

                <div class="Section1_text buy_app_box d-flex flex-column flex-lg-row justify-content-between align-items-center p-4">

                    <!-- Left Content -->
                    <div class="me-lg-4 text-center text-lg-start mb-3 mb-lg-0">
                        <h5 class="mb-2">
                     You're using <strong>WappPress BASIC (Free)</strong>
                        </h5>

                        <p class="mb-0">
                            Your generated Android app is valid for <strong>15 days only</strong>.
                            Upgrade to the <strong>PRO Version</strong> and enjoy:
                        </p>

                        <ul class="mb-0 mt-2">
                            <li>✅ Unlimited App Validity</li>
                            <li>✅ Lifetime Updates</li>
                            <li>✅ Unlimited Push Notifications</li>
                            <li>✅ One-Time Payment</li>
                        </ul>
                    </div>

                    <!-- Right CTA -->
                    <div class="text-center">

                        <a href="https://codecanyon.net/item/wapppress-builds-android-mobile-app-for-any-wordpress-website/10250300"
                           target="_blank"
                           class="btn btn-warning btn-lg px-4 fw-bold">
                            BUY PRO VERSION - $24
                        </a>

                        <div class="small mt-2 text-muted">
                            One-time payment • Lifetime updates • No monthly fees
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<?php } ?>


	<div class="contant-section1">
		
		<div class="section">

		<div class="wrapper">
<?php
if (
    !isset($_SERVER['HTTPS']) ||
    strpos($dirIncImg, 'http://') !== false
) {
    echo "<div id='supportId' class='msgAlert'>
            Your Website is not running on https.<br/>
            Please make sure SSL is installed and in Settings->General URLs are on https.
          </div>";
}
?>
			<div class="contant-section">
				<!--div id='settings'>&nbsp;</div-->
				<div class="setting-head">

						<h2>Build App</h2>
				</div>

				<?php
							$current_user = wp_get_current_user();
							$user_name=$current_user->user_login;
							$user_email=$current_user->user_email;
							?>
							

				<!--===Setting Box Start===--->
<div id='errorResponse' class='msgAlert'></div>
				<div class="setting-box">

					<div class="inner_left">

				

								

						<form role="form" action="#"  id="customer_support">

						<input type="hidden" name='dirPlgUrl1' id='dirPlgUrl1' value='<?php echo  esc_html($dirIncImg); ?>'/>

						<div class="setting-form">

							<div class="supportForms_input" style="display:none">

								<p>

									Name:- <br /><input type="text" name='name' id='name' value="<?php echo  esc_html($user_name);?>" />

								</p>

							</div>
							

							<div class="supportForms_input"  style="display:none">

								<p>

									Email:- <br /><input type="text" name='semail' id='semail'  value="<?php echo  esc_html($user_email);?>" />

								</p>

							</div>

							<br/>

							<div class="supportForms_input">

								<p>

									 App Name (<em><span class='fon_cls'>Choose a unique name related to your brand or website.</span></em>) :- <br /><input type="text" name='app_name' id='app_name' value="<?php echo  esc_html(@$options['wapppress_name']); ?>" />

								</p>

							</div>

				

							

							<!--==== Show Upload Div Start ====-->

							<div id="upload_logo_form">

								<div class="supportForms_input">

									<p>

										 App Launcher Icon Image (PNG/JPEG/JPG)  :- <br /><input type="file" name='app_logo' id='app_logo' />

									</p>

								</div>

							</div>

							<!--==== Show Upload Div End ====-->

							

					
							<!--==== Show Splash Upload Div Start ====-->

								<div id='upload_splash_form'>

									<div class="supportForms_input" >

										<p>

											App Splash Screen Image (PNG/JPEG/JPG)  :-<br />

											<input type="file" name='app_splash_image' id='app_splash_image' />

										</p>

									</div>

								</div>	


							
							<div class="supportForms_input">
								App Monetization:
									<p>

									<input style='width:0% !important' type="checkbox" name='adbmob_google' id='adbmob_google'  onclick='return show_AdMob();'  value='0'/>
									
									Google AdMob (<em><span class='fon_cls'>Banner/Interstitial/Banner/Rewarded</span></em>):-
								 <p id="show_adbmob_google" style="display:none">
							
									AdMob App ID:- <br /><input type="text" name='admob_app_id' id='admob_app_id' placeholder='e.g. ca-app-pub-3940256099942544~3347511713' />
									<br />
									Ad Type:
									<select name='admob_ad_type' class="form-select" aria-label="Default select" required>
									  <option selected>Select Ad Type</option>
									  <option value="1" selected>Banner</option>
									  <option value="2">Interstitial</option>
									  <option value="3">Rewarded</option>
									</select>
									<br />
													Enter Ad unit ID as per Ad Type( e.g Banner/Interstitial/Rewarded):- <br /><input type="text" name='admob_ad_unit_id' id='admob_ad_unit_id' placeholder='e.g. ca-app-pub-3940256099942544/6300978111' />

				
							
									

								 </p>
									

								</p>
							</div>
					
							<div class="supportForms_input">
							App Type:
									<p>
							<input style='width:0% !important' type="radio" name='app_type' id='app_type_aab'     value='1'/>									
									.aab (<em><span class='fon_cls'>Choose this option if you want to upload your app to Google play store.</span></em>)

								</p>
							</div>
							<div class="supportForms_input">
									<p>

									<input style='width:0% !important' type="radio" name='app_type' id='app_type_apk' checked   value='2'/>									
									.apk (<em><span class='fon_cls'>Choose this option if you don't want to upload your app to Google play store.</span></em>)
										</p>
										<?php 
										$license = get_option('wapppress_license', '');  
										if(empty($license)&&(! preg_match( '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i',  $license))) 
										{
										$license ='';
										}
										?>
										<input type="hidden" name='license' id='license' placeholder='CodeCanyon "Item Purchase Code"' value="<?php echo esc_attr($license); ?>" />
							</div>
							
							
																

							<div class="clear">

							</div>

							

							<div class="sve_change_btn sve_change_btn2">
											
								<div class="row">								
									<div class="col-md-6">
									<input id="submit" class='submit-build btn btn-info btn-lg'  type="submit" value="Build / Generate App" name="submit">
									<br/><br/>
									<?php 
									$recent = $this->get_recent_app_build();
	
									if ( $recent ) {

										$app_id=esc_html($recent['app']);
										$app_type=esc_html($recent['type']);
										$expires=ceil((72 * HOUR_IN_SECONDS - (time() - esc_html($recent['created_at']))) / 3600);
										$is_active_show='display:block';
										$apn_url =  'https://author.wapppress.com/app/?app=' . urlencode($app_id).'&type=' . urlencode($app_type);
										$dw_url = esc_url('https://author.wapppress.com/app/download.php?app='. urlencode($app_id).'&type=' . urlencode($app_type));
										$download_html='<a href="'.$dw_url.'"   class="btn btn-info-dwd  btn-lg submit-build"   role="button"> Download Your App</a>';
										
										if ($app_type == 'apk')
										{
											$app_help_apk='display:block';
											$app_help_aab='display:none';
										}else{
											$app_help_aab='display:block';
											$app_help_apk='display:none';
										}
										
									}else{
										$is_active_show='display:none';
										$app_help_apk='display:none';
										$app_help_aab='display:none';
										$download_html='';
										
									}

									
									?>
									<div id='dwnloakIdLink' >
										<?php echo  wp_kses_post($download_html);?>

									</div>
									</div>
									
									<div class="col-md-6">
										<span id="build-btn-load" style="display:none"> 
										<img src="<?php echo esc_url( plugins_url( '../images/loading-img.gif', __FILE__ ) ); ?>" alt="<?php esc_attr_e( 'Loading...', 'wapppress-builds-android-app-for-website' ); ?>" />
											</span>

									
										<div id='dwnloakId' style="<?php echo  esc_attr($is_active_show);?>; float:right;text-align:center" >

											<strong>Scan to Download your App:</strong><br/>
											<img src="https://qrcode.tec-it.com/API/QRCode?data=<?php echo  rawurlencode($apn_url);?>&backcolor=%23ffffff&size=small&quietzone=1&errorcorrection=H"  alt="QR Code" style="height:150px"/>
											<br/>Scan this QR code with your smartphone to download the app directory.
										</div>
									</div>
								</div>
								
								<!--p>
								<em><span class='fon_cls'>(Click on "BUILD/Generate App" butoon  to create app for your website now. )</span></em>	
								</p-->	
							</div>
							<!----------App Help Secction  Start--------->
							<div id='app_help_aab' style='<?php echo  esc_attr($app_help_aab);?>'>
								
								<section >
								  <strong><br/>How to Upload an .AAB File to Google Play Store, Here are the steps:</strong>

								  <ol>
									<li>
									  <strong>Sign in to Google Play Console</strong><br>
									  Go to 
									  <a href="https://play.google.com/console" target="_blank" rel="noopener">
										https://play.google.com/console
									  </a>
									  and log in with your Google developer account.
									</li>

									<li>
									  <strong>Select Your App</strong><br>
									  From the dashboard, choose the app you want to upload the AAB file for.
									  <br>
									  (If it’s a new app, click <em>Create app</em> and complete the basic details.)
									</li>

									<li>
									  <strong>Go to Production or Testing</strong><br>
									  In the left menu, navigate to:
									  <ul>
										<li><em>Release &gt; Production</em> (for live release), or</li>
										<li><em>Release &gt; Testing</em> (Internal / Closed / Open testing)</li>
									  </ul>
									</li>

									<li>
									  <strong>Create a New Release</strong><br>
									  Click <em>Create new release</em>.
									</li>

									<li>
									  <strong>Upload the .AAB File</strong><br>
									  Click <em>Upload</em> and select your <code>.aab</code> file from your computer.
									</li>

									<li>
									  <strong>Add Release Notes</strong><br>
									  Enter release notes describing what’s new in this version.
									</li>

									<li>
									  <strong>Review the Release</strong><br>
									  Click <em>Save</em>, then <em>Review release</em> to check for errors or warnings.
									</li>

									<li>
									  <strong>Submit for Review</strong><br>
									  Click <em>Submit</em>. Google will review your app before publishing.
									</li>
								  </ol>
							<strong><br/>Note: This App can be uploaded to Google Play Store, if you need help please contact us at <a href="mailto:info@wapppress.com">info@wapppress.com</a></strong>
								 
								</section>
							</div>
							
							<div id="app_help_apk" style='<?php echo  esc_attr($app_help_apk);?>'>
							  <strong>How to Install/Test Your App (.apk)</strong>
							  <ol>
							  <li>📷  open the <strong>Camera app</strong> on your Android phone and <strong>scan the QR code above</strong>, then tap the link that appears.</li>
								<li>OR👉 Tap the <strong>Download Your App</strong> button above to download the APK file.</li>
								
								<li>Go to <strong>Settings → Security</strong> (or <strong>Privacy</strong>) and enable <strong>Install unknown apps</strong>.</li>
								<li>Open the APK file from your <strong>Downloads</strong> folder.</li>
								<li>Tap <strong>Install</strong> and wait a few seconds.</li>
								<li>Tap <strong>Open</strong> and enjoy your app 🎉</li>
							  </ol>
							</div>

							
							<!----------App Help Secction end--------->
						
							<span style='color:#6D6D6D;font-size:13px;'><br/><b>Note:</b> <strong style='color: #0074a2;'>"BUILD/Generate App"</strong> feature will only  work  for the website/s hosted on live server, it would not work in localhost / local server.</span>

						</div>

						</form>

								

						

					
						
					</div>
					

					<div class="wrap-right mobileFrame">

						<iframe frameborder="0" allowtransparency="no" name="mobile_frame" id="mobile_frame" src="<?php echo esc_url(get_site_url()) ; ?>"/>

						</iframe>

					</div>


					<div class="clear">

					</div>

				

				<!--===Setting Box End===--->

				

				<!--===Android APP Box Start===--->

				<div id='bulid'>&nbsp;</div>
						

						

						

						

						

						<!---=== Launcher Upload PopUp Div  Start ===--->

							

							

						<!---=== Launcher Upload PopUp Div  End ===--->						

						<script type="text/javascript">

						jQuery(document).ready(function () {

							jQuery('#app_icon_img').hover(function() {

								jQuery("img#icon-preview").addClass('transition');

							}, function() {

								jQuery("img#icon-preview").removeClass('transition');

							});

							

							jQuery('input:radio[name="custom_splash_logo"]').filter('[value="0"]').attr('checked', true);

							jQuery('input:radio[name="custom_launcher_logo"]').filter('[value="0"]').attr('checked', true);

							

						});	
						//
							jQuery(window).load(function () {
									jQuery("#build-btn-load").hide();
							});	
						//
						function show_launcher_logo_form(fromId){

							if(fromId==0){

								jQuery('#upload_logo_form').show('slow');

								jQuery('#custom_logo_form').hide('fast');

							}else if(fromId==1){

								jQuery('#custom_logo_form').show('slow');

								jQuery('#upload_logo_form').hide('fast');

							}

							

						}

						

						

						

						function show_splash_screen_logo_form(fId){

							if(fId==0){

								jQuery('#upload_splash_form').show('slow');

								jQuery('#custom_splash_form').hide('fast');

							}else if(fId==1){

								jQuery('#custom_splash_form').show('slow');

								jQuery('#upload_splash_form').hide('fast');

							}

							

						}
						function show_AdMob()
						{
								
							if(jQuery('#adbmob_google').val()==0)
							{
								jQuery('#show_adbmob_google').show('slow');
								jQuery('#adbmob_google').val('1')
								
							}else{
								jQuery('#show_adbmob_google').hide('fast');
								jQuery('#adbmob_google').prop('checked', false);
								jQuery('#adbmob_google').val('0')
								
							}
										

						}
						

						jQuery.validator.addMethod("alphanumeric", function(value, element) {

							return this.optional(element) || /^[a-zA-Z0-9]+$/i.test(value);

						}, "Only allow alpha/numeric.");



						jQuery( "#upload_lanuchar_icon_form" ).validate({

									rules: {

										

									},

									messages: {

											

										},

										submitHandler: function(form) {

										 ajax_launchar_icon_form();

									}

							});

							jQuery("#upload_lanuchar_crop_icon_form" ).validate({

									submitHandler: function(form) {

										 ajax_launchar_crop_icon_form();

									}

							});

						

							jQuery( "#customer_support" ).validate({

									rules: {

										name:{

											required: true

										},

										semail: {

											required: true,

											email:true

										},

										

										app_logo_text: {

										  required: function() {

											var a_logo =jQuery('input:radio[name=custom_launcher_logo]:checked').val();

											 if (a_logo==1){

												 return true;

											 }else{

												 return false;

											 }

										  },

										  maxlength:5

										},

										 

										app_splash_text: {

										  required: function() {

											var splash_logo =jQuery('input:radio[name=custom_splash_logo]:checked').val();

											 if (splash_logo==1){

												 return true;

											 }else{

												 return false;

											 }

										  },

										  maxlength:10

										},

										app_name: {

											required: true

										}

									},

									messages: {

											name: {

												required: "Please enter your name."

											},

											semail: {

												required: "Please enter your email."

											},

											 

											app_name: {

												required: "Please enter only unique app name."

											},

											app_logo_text: {

												required: "Please enter your app icon text."

											},

											app_splash_text: {

												required: "Please enter your app splash screen text."

											}

										},

										submitHandler: function(form) {

										 ajax_wapp_api_form();

									}

							});

							</script>

						

				<!--===Android APP Box End===--->

				

			</div>
		</div>

	</div>

</div>

<?php require_once( 'footer.php' );

}

	//App Core Setting function	

	function register_settings() {

		// register_setting( $option_group, $option_name, $sanitize_callback )

		register_setting( 'wapppress_group', 'wapppress_settings', array($this, 'settings_validate') );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )

			{

				//

			}

	}

	

	function settings_validate($arr_input) {

		$frontpage_id =  get_option('page_on_front');

		$options = get_option('wapppress_settings');

		@$options['wapppress_name'] = trim( $arr_input['wapppress_name'] );

		@$options['wapppress_theme_switch'] = trim( $arr_input['wapppress_theme_switch'] );

		@$options['wapppress_theme_setting'] = trim( $arr_input['wapppress_theme_setting'] );

		if(!empty($arr_input['wapppress_home_setting'])){

			@$options['wapppress_home_setting'] =	trim( $arr_input['wapppress_home_setting']);

		}else{

			@$options['wapppress_home_setting'] =	trim( $frontpage_id );

		}

		@$options['wapppress_theme_author'] 		= trim( $arr_input['wapppress_theme_author'] );
		@$options['wapppress_theme_date'] 			= trim( $arr_input['wapppress_theme_date'] );
		@$options['wapppress_theme_comment'] 		= trim( $arr_input['wapppress_theme_comment'] );
		@$options['wapppress_push_post'] 			= trim( $arr_input['wapppress_push_post'] );
		@$options['wapppress_push_post_edit']		= trim( $arr_input['wapppress_push_post_edit'] );
		@$options['wapppress_push_product'] 		= trim( $arr_input['wapppress_push_product'] );
		@$options['wapppress_push_product_edit'] 	= trim( $arr_input['wapppress_push_product_edit'] );

		return $options;

	}

	

	// Theme Page 

	public function maker_theme_page(){

	require_once( 'header.php' );

	$args = array();

	$themes = wp_get_themes( $args );

	$dirIncImg  = trailingslashit( esc_url(plugins_url('wapppress-builds-android-app-for-website')) );

?>



<!--===Theme Listing Box Start===--->

<div class="contant-section1">	

	<div class="section">

		<div class="wrapper">

			<div class="contant-section">

				<h5>

				<img src="<?php echo  esc_html(plugins_url( '../images/img1.png',  __FILE__ )) ?>" title="" alt=""/> &nbsp; <i>All Themes Listing</i>

				</h5>

				<div class="wrapper">

					<div class="container_main">

						<?php $the = array(); foreach($themes as $theme_val => $theme_name){

						$options = get_option('wapppress_settings');

						$currentTheme= $options['wapppress_theme_setting'];

						if($currentTheme==$theme_val){

						$theme_img = get_theme_root_uri().'/'.$theme_val.'/'.'screenshot.png';

						$url = esc_url(add_query_arg( array('wapppress' => true,'theme' =>$currentTheme,), admin_url( 'customize.php' ) ));

						 ?>

						<div class="theme-box-main">

							<div class="theme_box">

								<span><img src="<?php echo  esc_html($theme_img)?>" alt="<?php echo  esc_html($theme_name)?>" width='244' height="225" /></span>

								<a class="customize" href="<?php  echo  esc_html($url); ?>">Customize</a>

							</div>

							<p>

								<img src="<?php echo esc_url(plugins_url( '../images/shadow.png',  __FILE__ )) ?>" title=""/>

							</p>

						</div>

						<?php } } ?>

						<?php

						$the = array(); foreach($themes as $theme_val => $theme_name){

						$options = get_option('wapppress_settings');

						$currentTheme= $options['wapppress_theme_setting'];

						if($currentTheme!=$theme_val){

						$theme_img = get_theme_root_uri().'/'.$theme_val.'/'.'screenshot.png';

						$nonce = wp_create_nonce('switch-theme_'.$theme_val);

						?>

						<div class="theme-box-main">

							<div class="theme_box">

								<span><img src="<?php echo  esc_html($theme_img); ?>" alt="<?php echo  esc_html($theme_name); ?>" width='244' height="225" /></span>

								<a class="customize" style="opacity:0.5;pointer-events: none;" href="<?php  echo  esc_html($url); ?>">Customize</a>

							</div>

							<p>

								<img src="<?php echo esc_url(plugins_url( '../images/shadow.png',  __FILE__ )) ?>" title=""/>

							</p>

						</div>

						<?php } } ?>

					</div>

					<div class="clear"></div>

				</div>

			</div>

		</div>

	</div>

</div>

<!--===Theme Listing Box End===--->



<?php require_once( 'footer.php' );

}	

// Activate Pro Page 

public function wapppress_pro_settings(){

require_once( 'header.php' );

$args =array();

$themes = wp_get_themes( $args );

$dirIncImg  = trailingslashit( esc_url(plugins_url('wapppress-builds-android-app-for-website')) );

$dirPath1  = trailingslashit( plugin_dir_path( __FILE__ ) );
/////////save license
if (isset( $_POST['license'], $_POST['wapppress_license_nonce'] ) &&
	wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wapppress_license_nonce'] ) ), 'wapppress_license_action' )
) {

	$license = sanitize_text_field( wp_unslash( $_POST['license'] ) );

	if ( ! preg_match(
		'/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i',
		$license
	) ) {
		$license = '';
	}

	update_option( 'wapppress_license', $license );
}

?>

<!--===Push Notification Box Start===--->

<div class="contant-section1">	

	<div class="section">

	<div class="wrapper">

		<div class="contant-section">

			<div class="setting-head">

				<h2>Activate WappPress Pro</h2>	

			</div>

			<div class="sec-2" style="border:none;">

				<div class="setting-sec">

				<div id='msgId' class='msgAlert'></div>
					<div class="setting-form" id='push_area'>
<?php	$license = get_option('wapppress_license', ''); // Fetch saved license key ?>
						<div class="headingIn">
					<p>
    <strong>WappPress Pro:</strong>
    This section is available only for Pro users. If you have already purchased WappPress Pro from Codecanyon, please enter your purchase code below to activate Pro features; otherwise, you can upgrade to unlock unlimited apps, unlimited push notifications, and premium support.
	<?php if(empty($license)&&(! preg_match( '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i',  $license))) 
	{		?>
    <a href="https://codecanyon.net/item/wapppress-builds-android-mobile-app-for-any-wordpress-website/10250300" target="_blank">
        Upgrade to WappPress Pro
    </a>
	<?php } ?>
</p>


						
						<form method="post" id="wapppress_license" action="">
						<?php wp_nonce_field( 'wapppress_license_action', 'wapppress_license_nonce' ); ?>

						<!--table class="form-table">
							<tr>
								<th scope="row">
									<label for="license">Item Purchase Code</label>
								</th>
								<td>
									<input type="text"
										   name="license"
										   id="license"
										   class="regular-text"
										   placeholder='Codecanyon Item Purchase Code'
										   value="<?php echo esc_attr( $license ); ?>" required />
									<p class="description">
										Example: <code>12345678-abcd-1234-abcd-1234567890ab</code>
										<br/><a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code"
										   target="_blank"><strong>Click here to find your purchase code </strong></a>
									</p>
								</td>
							</tr>
						</table-->

						<?php /* submit_button( 'Update License Settings' );*/ ?>
					</form>
					<script type="text/javascript">

						
							jQuery( "#wapppress_license" ).validate({

									rules: {

										license:{

											required: true

										}

									}
							});

							

							

							</script>

			

						</div>

						

						

						

					</div>

				
				


				</div>

			</div>

		</div>

	</div>

  </div>

</div>

<!--===Pro End===--->



<?php require_once( 'footer.php' );

}
// Faq Page 

public function wapppress_faq(){

require_once( 'header.php' );

?>

<!--===FAQ Box Start===--->

<div class="contant-section1">	

	<div class="section">

	<div class="wrapper">

		<div class="contant-section">

			

			<div class="sec-2" style="border:none;">

				<div class="setting-sec">

				
					<div class="setting-form" id='push_area'>

						<div class="headingIn">



					<div class="wapppress-faq">
						<h2>Frequently Asked Questions</h2>

						<div class="accordion" id="wapppressFaq">

							<!-- FAQ 1 -->
							<div class="accordion-item">
								<h2 class="accordion-header">
									<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#wpfaq1">
										What is WappPress Pro and what are its benefits?
									</button>
								</h2>
								<div id="wpfaq1" class="accordion-collapse collapse show">
									<div class="accordion-body">
										Apps created using WappPress Pro support unlimited app creation,
										unlimited push notifications, and include premium support.
									</div>
								</div>
							</div>

							<!-- FAQ 2 -->
							<div class="accordion-item">
								<h2 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wpfaq2">
										I have already purchased WappPress Pro. Where can I find my purchase code?
									</button>
								</h2>
								<div id="wpfaq2" class="accordion-collapse collapse">
									<div class="accordion-body">
										Visit the following link and follow the instructions:
										 
										<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code"
										   target="_blank">
											How to find your purchase code
										</a>
									</div>
								</div>
							</div>

							<!-- FAQ 3 -->
							<div class="accordion-item">
								<h2 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wpfaq3">
										How can I upgrade from WappPress Basic to WappPress Pro?
									</button>
								</h2>
								<div id="wpfaq3" class="accordion-collapse collapse">
									<div class="accordion-body">
										You can upgrade by purchasing WappPress Pro from Codecanyon:
									
										<a href="https://codecanyon.net/item/wapppress-builds-android-mobile-app-for-any-wordpress-website/10250300"
										   target="_blank">
											Upgrade to WappPress Pro
										</a>
									</div>
								</div>
							</div>

							<!-- FAQ 4 -->
							<div class="accordion-item">
								<h2 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wpfaq4">
										Does WappPress Pro have any recurring charges?
									</button>
								</h2>
								<div id="wpfaq4" class="accordion-collapse collapse">
									<div class="accordion-body">
										No. WappPress Pro is a one-time purchase and includes lifetime usage
										along with future updates.
									</div>
								</div>
							</div>

							<!-- FAQ 5 -->
							<div class="accordion-item">
								<h2 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wpfaq5">
										Do I need to download a separate Pro plugin after purchasing?
									</button>
								</h2>
								<div id="wpfaq5" class="accordion-collapse collapse">
									<div class="accordion-body">
										No. You can simply enter your WappPress Pro license key here,
										update the settings, and recreate your app.
									</div>
								</div>
							</div>

							<!-- FAQ 6 -->
							<div class="accordion-item">
								<h2 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wpfaq6">
										I created an app using WappPress Basic. Do I need to recreate it after upgrading?
									</button>
								</h2>
								<div id="wpfaq6" class="accordion-collapse collapse">
									<div class="accordion-body">
										Yes. After upgrading to WappPress Pro, you must recreate your app
										to activate Pro features.
										<br><br>
										You can do this by either:
										<ol>
											<li>Entering your Pro license key here and recreating the app, or</li>
											<li>Downloading WappPress Pro from Codecanyon and recreating the app</li>
										</ol>
									</div>
								</div>
							</div>

							<!-- FAQ 7 -->
							<div class="accordion-item">
								<h2 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wpfaq7">
										What should I do if I face issues while creating my app?
									</button>
								</h2>
								<div id="wpfaq7" class="accordion-collapse collapse">
									<div class="accordion-body">
										You can contact WappPress support by submitting a ticket here:
										<br>
										<a href="https://wapppress.freshdesk.com" target="_blank">
											https://wapppress.freshdesk.com
										</a>
									</div>
								</div>
							</div>
							<!-- FAQ 8 -->
							<div class="accordion-item">
								<h2 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wpfaq8">
										Will this break my WordPress website?
									</button>
								</h2>
								<div id="wpfaq8" class="accordion-collapse collapse">
									<div class="accordion-body">
										<b>No.</b> WappPress does not modify core WordPress files. It works as a standard plugin and can be safely activated or deactivated without affecting your website content or database.
									</div>
								</div>
							</div>
							<!-- FAQ 9 -->
							<div class="accordion-item">
								<h2 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wpfaq9">
										Is this Play Store compliant?
									</button>
								</h2>
								<div id="wpfaq9" class="accordion-collapse collapse">
									<div class="accordion-body">
										<b>Yes.</b> Apps generated using WappPress are designed to comply with Google Play Store policies and have been successfully published on the Play Store when standard guidelines (privacy policy, app content, permissions) are followed.
									</div>
								</div>
							</div>
							<!-- FAQ 10 -->
							<div class="accordion-item">
								<h2 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wpfaq10">
										Which Android versions are supported?
									</button>
								</h2>
								<div id="wpfaq10" class="accordion-collapse collapse">
									<div class="accordion-body">
										WappPress is tested with recent Android versions and works on a wide range of Android device
									</div>
								</div>
							</div>
							<!-- FAQ 11 -->
							<div class="accordion-item">
								<h2 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wpfaq11">
										How do I activate WappPress Pro?
									</button>
								</h2>
								<div id="wpfaq11" class="accordion-collapse collapse">
									<div class="accordion-body">
										Once you have purchased <b>WappPress Pro from Codecanyon</b>, copy your
										<b>purchase code</b> from Codecanyon.
										<br><br>
										You can find the purchase code as per the details given in the
										above FAQ.
										<br><br>
										Once you have the purchase code,
										<a href="admin.php?page=wapppresspro">
											click here to activate Pro
										</a>.
									</div>
								</div>
							</div>

							
						</div>
					</div>

						</div>

						

						

						

					</div>

				
				


				</div>

			</div>

		</div>

	</div>

  </div>

</div>

<!--===FAQ End===--->



<?php require_once( 'footer.php' );

}
// Push Notification Page 

public function maker_push_page(){

require_once( 'header.php' );

$args =array();

$themes = wp_get_themes( $args );

$dirIncImg  = trailingslashit( esc_url(plugins_url('wapppress-builds-android-app-for-website')) );

$dirPath1  = trailingslashit( plugin_dir_path( __FILE__ ) );

?>

<!--===Push Notification Box Start===--->

<div class="contant-section1">	

	<div class="section">

	<div class="wrapper">

		<div class="contant-section">

			<div class="setting-head">

				<h2>Push Notifications</h2>	

			</div>

			<div class="sec-2" style="border:none;">

				<div class="setting-sec">

				<div id='msgId' class='msgAlert'></div>
					<div class="setting-form" id='push_area'>

						<div class="headingIn">

							You can send messages/alerts or push notifications to all the app installations as and when you want to

							send. This message/alert would be delivered instantly to all the users who have installed your Mobile App. This would help in reaching out to your users for advertisement, new product notifications , offers or any message/alert that you want to sent to your users.

						</div>

						<form id='push_from' name='push_from'>

						

							<div class="supportForms_input">

								<p>Message:- <br /><textarea name="push_msg" id='push_msg'></textarea></p>

							</div>

							<br/>

							

							

							<input type="hidden" name='dirPath1' id='dirPath1' value='<?php echo  esc_html($dirPath1); ?>'/>

							<input type="hidden" name='dirPlgUrl1' id='dirPlgUrl1' value='<?php echo  esc_html($dirIncImg); ?>'/>

							

							<div class="sendAlert">

							<input id="push_btn" class="submit-build btn btn-info btn-lg" type="submit" value="Send Alert" name="push_btn">

							</div>

						</form>

						

						

						<script type="text/javascript">

						
							jQuery( "#push_from" ).validate({

									rules: {

										push_msg:{

											required: true

										}

									},

									messages: {

											push_msg: {

												required: "Please enter your message."

											}

										},

										submitHandler: function(form) {

										 ajax_wapp_push_form();

									}

							});

							

							

							</script>

					</div>

				
				


				</div>

			</div>

		</div>

	</div>

  </div>

</div>

<!--===Push Notification Box End===--->



<?php require_once( 'footer.php' );

}

//Create App 

// Create App
public function create_app() {
    // Verify the nonce
    if ( ! check_ajax_referer( 'wapppress_nonce', 'security', false ) ) {
        wp_send_json_error( 'Invalid nonce' );
        wp_die();
    }

    // Upload Launcher Icon Start
    if ( isset( $_FILES['app_logo'] ) && ! empty( $_FILES['app_logo']['name'] ) ) {
        $app_logo_name      = '';
        $new_app_logo_name  = 'ic_launcher.png';
        $push_icon_name     = 'ic_stat_gcm.png';

        if ( isset( $_FILES['app_logo']['error'] ) && $_FILES['app_logo']['error'] === UPLOAD_ERR_OK ) {
            $app_logo_name = sanitize_file_name( wp_unslash( $_FILES['app_logo']['name'] ) );
			$app_logo_temp ='';
			if(isset( $_FILES['app_logo']['tmp_name'] ) )
			{
            $app_logo_temp = sanitize_text_field( wp_unslash( $_FILES['app_logo']['tmp_name'] ) );
			}
        } else {
            wp_send_json_error( 'Invalid logo upload' );
            wp_die();
        }
    }
    // Upload Launcher Icon End

    // Upload Splash Image Start
    if ( isset( $_FILES['app_splash_image'] ) && ! empty( $_FILES['app_splash_image']['name'] ) ) {
        $app_splash_image    = '';
        $new_app_splash_image1 = 'splash_screen.png';

        if ( isset( $_FILES['app_splash_image']['error'] ) && $_FILES['app_splash_image']['error'] === UPLOAD_ERR_OK ) {
            $app_splash_image = time() . '_' . sanitize_file_name( wp_unslash( $_FILES['app_splash_image']['name'] ) );
			$app_splash_temp  = '';
			if(isset( $_FILES['app_splash_image']['tmp_name'] ) )
			{
             $app_splash_temp  = sanitize_text_field( wp_unslash( $_FILES['app_splash_image']['tmp_name'] ) );
			}
			
           
        } else {
            wp_send_json_error( 'Invalid splash upload' );
            wp_die();
        }
    }
    // Upload Splash Image End

    // Android API Form Start
    if ( isset( $_POST['type'] ) && sanitize_text_field( wp_unslash( $_POST['type'] ) ) === 'api_create_form' ) {
        $name   = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
        $email  = isset( $_POST['semail'] ) ? sanitize_email( wp_unslash( $_POST['semail'] ) ) : '';

        if ( function_exists( 'wapp_site_url' ) ) {
            $website = wapp_site_url();
        } else {
            $website = site_url();
        }

        $dirPlgUrl1 = isset( $_POST['dirPlgUrl1'] ) ? esc_url_raw( wp_unslash( $_POST['dirPlgUrl1'] ) ) : '';
        $ap         = isset( $_POST['ap'] ) ? sanitize_text_field( wp_unslash( $_POST['ap'] ) ) : '';
        $ip         = isset( $_POST['ip'] ) ? sanitize_text_field( wp_unslash( $_POST['ip'] ) ) : '';
        $file       = isset( $_POST['file'] ) ? sanitize_text_field( wp_unslash( $_POST['file'] ) ) : '';
		//////////////
        $dwn_type       = isset( $_POST['dwn_type'] ) ? sanitize_text_field( wp_unslash( $_POST['dwn_type'] ) ) : '';

        $domain_name  = $this->get_domain( $website );
        $domain_arr   = explode( '.', sanitize_text_field( $domain_name ) );
        $domain_fname = isset( $domain_arr[0] ) ? sanitize_text_field( $domain_arr[0] ) : '';
        $app_name     = isset( $_POST['app_name'] ) ? sanitize_text_field( wp_unslash( $_POST['app_name'] ) ) : '';

        // Get and encode logo
        $base64_app_logo = '';
        if ( ! empty( $app_logo_temp ) && file_exists( $app_logo_temp ) ) {
            $base64_app_logo = base64_encode( file_get_contents( $app_logo_temp ) );
        }

        // Get and encode splash
        $base64_app_splash = '';
        if ( ! empty( $app_splash_temp ) && file_exists( $app_splash_temp ) ) {
            $base64_app_splash = base64_encode( file_get_contents( $app_splash_temp ) );
        }

        $data = array(
            'name'              => $name,
            'app_name'          => $app_name,
            'base64_app_logo'   => $base64_app_logo,
            'base64_app_splash' => $base64_app_splash,
            'email'             => $email,
            'license'           => isset( $_POST['license'] ) ? sanitize_text_field( wp_unslash( $_POST['license'] ) ) : '',
            'admob_app_id'      => isset( $_POST['admob_app_id'] ) ? sanitize_text_field( wp_unslash( $_POST['admob_app_id'] ) ) : '',
            'admob_ad_type'     => isset( $_POST['admob_ad_type'] ) ? sanitize_text_field( wp_unslash( $_POST['admob_ad_type'] ) ) : '',
            'admob_ad_unit_id'  => isset( $_POST['admob_ad_unit_id'] ) ? sanitize_text_field( wp_unslash( $_POST['admob_ad_unit_id'] ) ) : '',
            'website'           => esc_url_raw( $website ),
            'domain_name'       => $domain_name,
            'domain_fname'      => $domain_fname,
            'app_site_url'      => $dirPlgUrl1,
        );

        $custom_launcher_logo = isset( $_POST['custom_launcher_logo'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_launcher_logo'] ) ) : '';
        $custom_splash_logo   = isset( $_POST['custom_splash_logo'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_splash_logo'] ) ) : '';

        if ( $custom_launcher_logo === '0' ) {
            $data['app_launcher_logo_name'] = 'ic_launcher.png';
            $data['app_push_icon']          = 'ic_stat_gcm.png';
        } elseif ( $custom_launcher_logo === '1' ) {
            $data['app_logo_color']            = isset( $_POST['app_logo_color'] ) ? sanitize_text_field( wp_unslash( $_POST['app_logo_color'] ) ) : '';
            $data['app_logo_text_color']       = isset( $_POST['app_logo_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['app_logo_text_color'] ) ) : '';
            $data['app_logo_text']             = isset( $_POST['app_logo_text'] ) ? sanitize_text_field( wp_unslash( $_POST['app_logo_text'] ) ) : '';
            $data['app_logo_text_font_family'] = isset( $_POST['app_logo_text_font_family'] ) ? sanitize_text_field( wp_unslash( $_POST['app_logo_text_font_family'] ) ) : '';
            $data['app_logo_text_font_size']   = isset( $_POST['app_logo_text_font_size'] ) ? sanitize_text_field( wp_unslash( $_POST['app_logo_text_font_size'] ) ) : '';
        }

        if ( $custom_splash_logo === '0' ) {
            $data['app_splash_screen_name'] = 'splash_screen.png';
        } elseif ( $custom_splash_logo === '1' ) {
            $data['app_splash_color']            = isset( $_POST['app_splash_color'] ) ? sanitize_text_field( wp_unslash( $_POST['app_splash_color'] ) ) : '';
            $data['app_splash_text']             = isset( $_POST['app_splash_text'] ) ? sanitize_text_field( wp_unslash( $_POST['app_splash_text'] ) ) : '';
            $data['app_splash_text_color']       = isset( $_POST['app_splash_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['app_splash_text_color'] ) ) : '';
            $data['app_splash_text_font_family'] = isset( $_POST['app_splash_text_font_family'] ) ? sanitize_text_field( wp_unslash( $_POST['app_splash_text_font_family'] ) ) : '';
            $data['app_splash_text_font_size']   = isset( $_POST['app_splash_text_font_size'] ) ? sanitize_text_field( wp_unslash( $_POST['app_splash_text_font_size'] ) ) : '';
        }
		////////////////////////////////////////////
		
		if ( ! empty($domain_name) && ! empty($dwn_type) ) {

			update_option(
				'wapppress_last_build',
				[
					'app'        => sanitize_text_field($domain_name),
					'type'       => sanitize_text_field($dwn_type),
					'created_at' => time(),
				],
				false // no autoload
			);
		}

		////////////////////////////////////////////
        $this->wcurlrequest( $ip . $ap . $file, $domain_name, $app_name, $data );
		
    }
    // Android API Form End
}

 // Function to extract domain
 public function get_domain($url)
 {
	   $pieces = wp_parse_url(esc_url_raw($url));
		$domain = isset($pieces['host']) ? sanitize_text_field($pieces['host']) : '';

		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,10})$/i', $domain, $regs)) {
			function wapppress_isLetter($domain_name) {
				return preg_match('/^\s*[a-z,A-Z]/', $domain_name) > 0;
			}

			if (wapppress_isLetter($regs['domain'])) {
				return sanitize_text_field($regs['domain']);
			} else {
				return "com_" . sanitize_text_field($regs['domain']);
			}
		}
		return false;
 }
public function wcurlrequest($ac, $d_name, $an, $data)
 {
		 
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= sanitize_text_field($key) . '=' . sanitize_text_field($value) . '&';
        }
        rtrim($fields, '&');
        
        // WP HTTP API for POST request
        $url = esc_url_raw($ac); // Escaping the URL
        $args = array(
            'method'      => 'POST',
            'timeout'     => 300,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'User-Agent' => !empty($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash( $_SERVER['HTTP_USER_AGENT'])) : 'Mozilla/5.0 (X11; U; Linux x86_64; pl-PL; rv:1.9.2.22) Gecko/20110905 Ubuntu/10.04 (lucid) Firefox/3.6.22',
            ),
            'body'        => $fields,
            'cookies'     => array(),
            'sslverify'   => false,
        );

        $response = wp_safe_remote_post($url, $args);
        $result = wp_remote_retrieve_body($response);

        if ($result != 0) {
            if ($result == 5) {
                $str = "5~test";
                wp_send_json_success($str);
                exit();
            } else if ($result == 9) {
                $str = "9~test";
                wp_send_json_success($str);
                exit();
            } else {
                global $wpdb;
                $d_name = esc_html(str_replace("-", "_", sanitize_text_field($d_name)));
                $str = '1' . '~' . $d_name;
                wp_send_json_success($str);
                exit();
            }
        } else {
            setcookie('wapppress_proxy', 'true', time() + (DAY_IN_SECONDS * 100));
            $str = "0~test---uv-".$result;
            wp_send_json_success($str);
            exit();
        }
 }

//Create App end
public function create_push_app() {
    // Verify the nonce
    if ( ! check_ajax_referer( 'wapppress_nonce', 'security', false ) ) {
        wp_send_json_error( 'Invalid nonce' );
        wp_die();
    }

    // Push Notification Form Start
    if ( isset( $_POST['type'] ) && sanitize_text_field( wp_unslash( $_POST['type'] ) ) === 'push_form' ) {

        $dirPath = dirname( __FILE__ );

        if ( function_exists( 'wapp_site_url' ) ) {
            $website = wapp_site_url();
        } else {
            $website = site_url(); // Or use home_url()
        }

        // Sanitizing and escaping data
        $domain_name = $this->get_domain( $website );

        // Collect POST data safely
        $ap       = isset( $_POST['ap'] ) ? sanitize_text_field( wp_unslash( $_POST['ap'] ) ) : '';
        $ip       = isset( $_POST['ip'] ) ? sanitize_text_field( wp_unslash( $_POST['ip'] ) ) : '';
        $file     = isset( $_POST['file'] ) ? sanitize_text_field( wp_unslash( $_POST['file'] ) ) : '';
        $push_msg = isset( $_POST['push_msg'] ) ? sanitize_text_field( wp_unslash( $_POST['push_msg'] ) ) : '';

        // 🔹 Ensure $get_contant is defined somewhere or replace with correct value
        $data = array(
            'push_msg'     => $push_msg,
            'domain_name'  => $domain_name,
            'app_auth_key' => isset( $get_contant ) ? sanitize_text_field( $get_contant ) : '',
        );

        if ( ! empty( $ip ) && ! empty( $ap ) && ! empty( $file ) ) {
            $this->wcurlpushrequest( $ip . $ap . $file, $data );
        } else {
            wp_send_json_error( 'Missing required parameters.' );
        }
    }
    // Push Notification Form End
}

// Function to send push notification request via cURL
 public   function wcurlpushrequest($ac, $data) {
               
        $args = array(
            'method'      => 'POST',
            'timeout'     => 300,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'User-Agent' => !empty($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash( $_SERVER['HTTP_USER_AGENT'])) : 'Mozilla/5.0',
            ),
            'body'        => $data,
            'cookies'     => array(),
            'sslverify'   => false,
        );

        $response = wp_safe_remote_post(esc_url_raw($ac), $args);

        if (is_wp_error($response)) {
            // Handle error
            echo 'Error: ' . esc_html($response->get_error_message());
            return;
        }

        $result = wp_remote_retrieve_body($response);

        // Handle response based on the result
        if ($result == 1) {
            wp_send_json_success('1');
        } elseif ($result == 4) {
            wp_send_json_success('4');
        } else {
            wp_send_json_success('0');
        }
        exit();
    }
//Custom Push Notification Start
public function send_custom_push_app( $push_msg ) {

	$website = home_url();

	// Replace wapppress_get_domain_name_custom with direct conditions
	$pieces = wp_parse_url( $website );
	$domain = isset( $pieces['host'] ) ? $pieces['host'] : '';

	$domain_name = false;

	if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,10})$/i', $domain, $regs ) ) {

		// Check if domain starts with letter
		if ( preg_match( '/^\s*[a-zA-Z]/', $regs['domain'] ) ) {

			$domain_name = $regs['domain'];

		} else {

			$domain_name = 'com_' . $regs['domain'];

		}
	}

	/////////////////////////////////////////////////////////////////////////////////////

	$ip   = 'http://199.38.85.107/aapi';
	$file = 'api-push-msg-v.0.4-t.php';

	$data = array(
		'push_msg'     => $push_msg,
		'domain_name'  => $domain_name,
		'app_auth_key' => isset( $get_contant ) ? sanitize_text_field( $get_contant ) : '',
	);

	$url = trailingslashit( $ip ) . $file;

	$args = array(
		'method'      => 'POST',
		'timeout'     => 300,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers'     => array(
			'User-Agent' => ! empty( $_SERVER['HTTP_USER_AGENT'] )
				? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) )
				: 'Mozilla/5.0',
		),
		'body'        => $data,
		'cookies'     => array(),
		'sslverify'   => false,
	);

	$response = wp_safe_remote_post( $url, $args );

	$result = wp_remote_retrieve_body( $response );

	return $result;
}
// Custom Push Notification End

//Search Home Page  
public function search_post_results() {

    // Check if POST variables exist
    $searchVal = isset($_POST['search_val']) ? sanitize_text_field(wp_unslash($_POST['search_val'])) : '';
    $nonceVal  = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

    // Verify nonce
    if ( empty($searchVal) || empty($nonceVal) || ! wp_verify_nonce($nonceVal, 'wapppress_group-options') ) {
        wp_send_json_error('<p>' . __( 'Security check failed', 'wapppress-builds-android-app-for-website' ) . '</p>');
    }

    if ( empty( $searchVal ) ) {
        wp_send_json_error('<p>' . __( 'Please Try Again', 'wapppress-builds-android-app-for-website' ) . '</p>');
    }

    global $wpdb;

    $args = array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        's'              => $searchVal,
        'posts_per_page' => 10,
        'fields'         => 'ids', // Get only post IDs
    );

    $query = new WP_Query($args);
    $allResults = $query->posts;

    if ( empty( $allResults ) ) {
        wp_send_json_error('<p>' . __('No Results Found', 'wapppress-builds-android-app-for-website' ) . '</p>');
    }

    // Build results list
    $str = '<p>' . __('Please choose a page', 'wapppress-builds-android-app-for-website' ) . '</p>';
    $str .= '<ol>';
    foreach ( $allResults as $postID ) {
        $str .= '<li><a href="javascript:void(0)" onclick="custom_page(' . esc_attr($postID) . ')" data-postID="' . esc_attr($postID) . '">'
                . esc_html(get_the_title( $postID )) 
                . '</a></li>';
    }
    $str .= '</ol>';

    wp_reset_postdata();
    wp_send_json_success( $str );
}


	///
function send_push_on_new_post( $post_id, $post ) {
    // Ensure HTTP_REFERER exists and sanitize it
    $referer = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw(wp_unslash($_SERVER['HTTP_REFERER'])) : '';

    if ( strpos($referer, 'edit') !== false ) {
        // Your action if the post is edited
        $post_title = sanitize_text_field($post->post_title);
        $post_type  = sanitize_text_field($post->post_type);
        $this->send_custom_push_app($post_title);
    } else {
        // Send push if the post is just published
        $post_title = sanitize_text_field($post->post_title);
        $post_type  = sanitize_text_field($post->post_type);
        $this->send_custom_push_app($post_title);
    }
}
			
function send_push_on_product( $new_status, $old_status, $post ) 
{
	if ( 'product' !== $post->post_type ) {
		return;
	}

	if ( 'publish' !== $new_status ) {
		return;
	}

	if ( 'publish' === $old_status ) {
		// 'Editing an existing product';
		$post_title = $post->post_title;
		$post_type  = $post->post_type ;
		send_custom_push_app($post->post_title);
	} else {
		// 'Adding a new product';
		$post_title = $post->post_title;
		$post_type  = $post->post_type ;
		send_custom_push_app($post->post_title);
	}
}
/**
	 * Insert action links.
	 *
	 * Adds action links to the plugin list table
	 *
	 * Fired by `plugin_action_links` filter.
	 *
	  */
	public function wappPress_insert_action_links( $links ) {
			
		$buildapp=esc_url('admin.php?page=wapppresssettings');

		$buildapp_link = sprintf( '<a href="%s"  style="color: #002bff; font-weight: bold;">%s</a>', $buildapp, __( 'Build App', 'wapppress-builds-android-app-for-website' ) );
		$new_links = array( $buildapp_link );
		$buy_link=esc_url('https://codecanyon.net/item/wapppress-builds-android-mobile-app-for-any-wordpress-website/10250300');
//////////////////////////
$license = get_option('wapppress_license', '');  
 if(empty($license)&&( !preg_match( '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i',  $license))) 
	{
		$pro_link = sprintf( '<a href="%s" target="_blank" style="color: #FF6000; font-weight: bold;">%s</a>', $buy_link, __( 'Get Pro', 'wapppress-builds-android-app-for-website' ) );

			// Add the promotional link to the array.
			array_push( $new_links, $pro_link );
	}
		///////////////////////////	
			$new_links = array_merge( $links, $new_links );
    return $new_links;
	}
function wapppress_check_trial() 
{
	$nonce = isset( $_POST['nonce'] )? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ): '';
    if ( empty( $nonce) ) {
        wp_send_json_error( 'Nonce missing' );
    }

    if ( ! wp_verify_nonce($nonce, 'wapppress_nonce' ) ) {
        wp_send_json_error( 'Nonce invalid' );
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Permission denied' );
    }

    $response = wp_remote_post(
        'https://wapppress.com/api_app_validity.php',
        array(
            'timeout' => 5,
            'body'    => array(
                'domain' => sanitize_text_field(
                    wp_parse_url( home_url(), PHP_URL_HOST )
                ),
            ),
        )
    );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( $response->get_error_message() );
    }

    $raw = wp_remote_retrieve_body( $response );

    if ( empty( $raw ) ) {
        wp_send_json_error( 'Empty API response' );
    }

    $body = json_decode( $raw, true );

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        wp_send_json_error( 'Invalid JSON from server' );
    }

    if ( empty( $body['message'] ) ) {
        wp_send_json_error( 'Message missing from API' );
    }

    //wp_send_json_success( $body );
	set_transient(
    'wapppress_trial_notice',
    [
        'message'   => $body['message'],
        'days_left' => intval( $body['days_left'] ),
    ],
    12 * HOUR_IN_SECONDS
);

wp_send_json_success();

}

function wappPress_trial_expired_notice() 
	{

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $data = get_transient( 'wapppress_trial_notice' );

    if ( empty( $data['message'] ) ) {
        return;
    }
	/////////check Pro
	$license = get_option('wapppress_license', '');  
 if(!empty($license)&&( preg_match( '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i',  $license))) 
	{
		  return;
	}		
    ?>

		<?php 
		if(($data['days_left']>0)&&($data['days_left']<5)){
			
			?>
		
	    <div class="notice notice-warning is-dismissible">
            <strong>
                Your mobile app will become inactive
                <?php echo esc_html( $data['message']  ); ?>,
                as the WappPress plugin trial period is ending.
                To continue using your mobile app and the WappPress plugin without interruption,
                please
                <a href="https://codecanyon.net/item/wapppress-builds-android-mobile-app-for-any-wordpress-website/10250300"
                   target="_blank" rel="noopener noreferrer">
                    purchase the WappPress Pro version here
                </a>
                or contact us at
                <a href="mailto:info@wapppress.com">info@wapppress.com</a>
            </strong>
			  </div>
			<?php
			
			}else if($data['days_left']<=0){?>
			<div class="notice notice-warning is-dismissible">
			    <strong>
                Your mobile app is currently inactive because the WappPress plugin trial period has ended.
                To restore access and continue using your mobile app and the WappPress plugin without interruption, please <a href="https://codecanyon.net/item/wapppress-builds-android-mobile-app-for-any-wordpress-website/10250300"
                   target="_blank" rel="noopener noreferrer">
                    purchase the WappPress Pro version here
                </a>
                or contact us at
                <a href="mailto:info@wapppress.com">info@wapppress.com</a>
            </strong>
			  </div>
			<?php } ?>
        
  
    <?php
}

	/**
	 * Plugin row meta.
	 *
	 * Extends plugin row meta links
	 *
	 * Fired by `plugin_row_meta` filter.
	 *
	 * @since 3.8.4
	 * @access public
	 *
	 * @param array  $meta array of the plugin's metadata.
	 * @param string $file path to the plugin file.
	 *
	 *  @return array An array of plugin row meta links.
	 */
	public function wappPress_plugin_row_meta( $links, $file  ) 
	{
		// Main plugin file basename
		$plugin_base = plugin_basename( dirname( __DIR__ ) . '/wappPress.php' );

		if ( $file !== $plugin_base ) {
			return $links;
		}
		
		$support_link = 'https://wapppress.freshdesk.com';
		 $row_links = array(
				// Add "Help" link pointing to support documentation.
				'help' => '<a href="https://wapppress.freshdesk.com" aria-label="' . esc_attr( __( 'wapppress-builds-android-app-for-website', 'wapppress-builds-android-app-for-website' ) ) . '" target="_blank">' . __( 'Need Help', 'wapppress-builds-android-app-for-website' ) . '</a>',
				// Add "Rate the wappPress plugin" link pointing to WordPress.org reviews page.
				'rate'   => '<a href="https://wordpress.org/support/plugin/wapppress-builds-android-app-for-website/reviews/#new-post" aria-label="' . esc_attr( __( 'Rate plugin', 'wapppress-builds-android-app-for-website' ) ) . '" target="_blank">' . __( 'Rate the plugin ★★★★★', 'wapppress-builds-android-app-for-website' ) . '</a>',
			);

		// Return the modified meta array.
	 return array_merge( $links, $row_links );
	}
	
	/* Get already created app details */
	private function get_recent_app_build()
	{

		$data = get_option('wapppress_last_build');

		if ( ! is_array($data) ) {
			return false;
		}

		$max_age = 72 * HOUR_IN_SECONDS;

		if ( empty($data['created_at']) || (time() - (int)$data['created_at']) > $max_age ) {
			return false;
		}

		return $data;
	}

	
}

new wappPress_admin_setting();

