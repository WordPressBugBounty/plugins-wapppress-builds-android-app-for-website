<?php
class wappPress_admin_setting extends wappPress {

	function __construct() {

			add_action( 'admin_menu', array( $this, 'maker_menu' ), 7);

			add_action( 'admin_init', array( $this, 'register_settings' ) );

			add_action( 'wp_ajax_create_app', array( $this, 'create_app' ) );

			add_action( 'wp_ajax_create_push_app', array( $this, 'create_push_app' ) );

			add_action( 'wp_ajax_get_app', array( $this, 'get_app' ) );

			add_action( 'wp_ajax_search_post_handler', array( $this, 'search_post_results' ) );

			if ( isset( $_GET['clear_app_cookie'] ) && 'true' === $_GET['clear_app_cookie'] ) {

				  self::reset_cookie();

			}
			
				//Custom Post New
		if(@$options['wapppress_push_post']=='on'){			
			add_action( 'publish_post', 'send_push_on_new_post', 10, 3 );
			}		
	    if(@$options['wapppress_push_post_edit']=='on'){			
			add_action( 'publish_post', 'send_push_on_new_post', 10, 3 );
			}
		if(@$options['wapppress_push_product']=='on'){			
			add_action( 'transition_post_status', 'send_push_on_product', 10, 3 );
			}		
	    if(@$options['wapppress_push_product_edit']=='on'){			
			add_action( 'transition_post_status', 'send_push_on_product', 10, 3 );
			}			

			
	}

	public function maker_menu() {

		$dirPlgUrl  = trailingslashit( esc_url(plugins_url('wapppress-builds-android-app-for-website')) );

		$pageTitle = __( 'WappPress', 'WappPress' );

		$maPlgin = 'wapppressplugin';

		$maSett = 'wapppresssettings';

		$maTheme = 'wapppresstheme';

		$maPush = 'wapppresspush';

		$plgIcon  = $dirPlgUrl  . 'images/view.png';

		$dirInc1  = $dirPlgUrl  . 'includes/';

		

		// Create main menu 

		$mainMenu = add_menu_page( $pageTitle,  __( 'wappPress BASIC', 'wappPress' ), 'manage_options', $maPlgin, array( $this, 'maker_basic_page' ),$plgIcon  );

		global $submenu;

		// Settings page sub menu

		$subSettingMenu = add_submenu_page($maPlgin, __( 'Settings', 'wappPress' ), __( 'Settings', 'wappPress' ),  'manage_options', $maSett, array( $this, 'maker_settings_page' ));
		
		$subPushMenu = add_submenu_page($maPlgin, __( 'Push Notification', 'wappPress' ), __( 'Push Notification', 'wappPress' ),  'manage_options', $maPush, array( $this, 'maker_push_page' ));

		$subThemeMenu = add_submenu_page($maPlgin, __( 'Themes', 'wappPress' ), __( 'Themes', 'wappPress' ),  'manage_options', $maTheme, array( $this, 'maker_theme_page' ));

				

	}

	

	//Basic Page 

	public function maker_basic_page(){

	require_once(  'header.php' );

	?>

	<div class="contant-section1">

	<div class="section">

		<div class="wrapper">

			<div class="contant-section">

				<h5>

				<img src="<?php echo esc_url(plugins_url( '../images/img1.png',  __FILE__ )) ?>" title="img1" alt="img1"/> &nbsp; <i>Build Android App in real-time for any wordpress website</i>

				</h5>

				<h3>

				WappPress <span>BASIC VERSION<span> &nbsp; &nbsp;<strong>(free)</strong>

				</h3>

				<p>

					With WappPress Basic Version you will enjoy following features

				</p>

				<div class="inner-contant">

					<div class="list-sec">

						<ul>
							<li>Push Notification <span style='color: red;display: inline;float: none;'>(New)</span></li>
							<li>Monetize Your App  with Google AdMob Interstitial Ads <span style='color: red;display: inline;float: none;'>(New)</span></li>
							<li><strong style='color: red;'>Android App Validity - 15 Days</strong></li>
							<li>Select different home page for Mobile app</li>

							<li>Select Different theme for website & mobile app   </li>

							<li>Select and customize launcher icon</li>

							<li>Upload your own custom icon</li>

							<li>Select and customize splash screen</li>

							<li>Upload your own splash screen </li>

							<span style='font-size:10px;margin-left:20px;'>( You can upload your own splash screen image, this will be used to capture the user's attention for a short time as a promotion or lead-in)</span><br /><br />

							<li>Ads Free - i.e. no ads/brand name include inside</li>

							<li>Allow to Build Android App in Real Time</li>

							

						</ul>

						<span><a href="<?php echo esc_url(admin_url('admin.php?page=wapppresssettings')); ?>"><img src="<?php echo esc_url(plugins_url( '../images/btn.png',  __FILE__ )) ?>" title="" alt=""/></a></span>

					</div>

					<div class="img-box img-box2">

						<img src="<?php echo esc_url(plugins_url( '../images/mob.png',  __FILE__ )) ?>" title="" alt=""/>

					</div>

					<div class="clear">

					</div>

				</div>

				<div class="sec-2">

					<div class="left-heading">

						<h3>

						WappPress <span class="pro-version">PRO VERSION</span>

						</h3>

					</div>

					<div class="right-heading">

						<h3><span>(FOR JUST &nbsp;<strong>$24</strong> &nbsp; ONLY )</span></h3>

					</div>

					<div class="clear">

					</div>

					<p>

						Use WappPress Pro Version to enjoy following features

					</p>

					<div class="inner-contant">

						<div class="list-sec1">

							<ul>
								<li>Push Notification <span style='color: red;display: inline;float: none;'>(New)</span></li>
								<li>Monetize Your App  with Google AdMob Interstitial Ads <span style='color: red;display: inline;float: none;'>(New)</span></li>
								<li><strong style='color: red;'>Android App Validity - Unlimited Time</strong></li>
								<li>Select different home page for Mobile app</li>

								<li>Select Different theme for website & mobile app</li>

								<li>Select and customize launcher icon</li>

								<li>Upload your own custom icon</li>

								<li>Select and customize splash screen</li>

								<li>Upload your own splash screen </li>

								<span style='font-size:10px;float:left;margin-left:24px;'>( You can upload your own splash screen image, this will be used to capture the user's attention for a short time as a promotion or lead-in)</span><br /><br />

								
								<li>Ads Free - i.e. no ads/brand name include inside</li>

								<li>Allow to Build Android App in Real Time</li>

							</ul>

							

							<span><a href="http://goo.gl/bcEb25" target='_blank'  ><img src="<?php echo esc_url(plugins_url( '../images/btn2.png',  __FILE__ )) ?>" title="" alt=""/></a></span>

							<span>

							<h2>$24 <strong>Only</strong></h2>

							</span>

						</div>

						<div class="img-box">

							<img src="<?php echo esc_url(plugins_url( '../images/mob.png',  __FILE__ )) ?>" title="" alt=""/>

						</div>

						<div class="clear">

						</div>

					</div>

				</div>

				<div class="sec-3">

					<h3> Publish App </h3>

					<p>

						If you need any help regarding publishing your app on Google Play <span><a href="mailto:info@wapppress.com">contact US</a></span>

					</p>

				</div>

			</div>

		</div>

	</div>	

	</div>	

	

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

	<div class="Section1">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="Section1_text">
					<p>You are using <b>WappPress BASIC VERSION (free)</b>, your Android App Validity is 15 days, <b>BUY PRO VERSION</b> to get app Validity for Unlimited Time</p>
					<a href="http://goo.gl/bcEb25" target='_blank' style="color:#f89400"  ><button>BUY PRO VERSION $24 Only</button></a>
				</div>
			</div>
		</div>
	</div>
</div>
	<div class="contant-section1">
		
		<div class="section">

		<div class="wrapper">

			<div class="contant-section">
				<!--div id='settings'>&nbsp;</div-->
				<div class="setting-head">

					<h3>SETTINGS</h3>

					<img src="<?php echo esc_url(plugins_url( '../images/line.png',  __FILE__ )) ?>" title="" alt=""/>

				</div>

				

				<!--===Setting Box Start===--->

				<div class="setting-box">

					<div class="inner_left">

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

											<li>

											<h3>Enable/Disable theme setting on desktop</h3>

											<span><img src="<?php echo esc_url(plugins_url( '../images/arrow.png',  __FILE__ )) ?>" alt=""></span>

											<div class="panel">

												<p>

													<input type="radio" name="wapppress_settings[wapppress_theme_switch]"<?php checked( $check, 'on'.false ); ?> value='on' /> Enable &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" value=''  name="wapppress_settings[wapppress_theme_switch]" <?php checked( $check, ''.false ); ?> /> Disable

												</p>

											</div>

											</li>

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

													echo	$the[ $theme_val ] = '<option id="'.$src.'" value="'. $theme_val .'" '. selected( @$options['wapppress_theme_setting'],$theme_val, false) .'>'. esc_html( $theme_name ) .'</option>

														'."\n"; 

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

										<input id="submit" style='padding:0 !important'  type="image" src="<?php echo esc_url(plugins_url( '../images/btn3.png',  __FILE__ )) ?>" value="Save Changes" name="submit">

									</div>

									<div style='margin-top: 15px;'>

									<a href='#bulid'><img src='<?php echo esc_url(plugins_url( '../images/btn6.png',  __FILE__ )) ?>' /></a>

									</div>

								</div>

								</form>

								

							</div>

						</div>

					</div>

					<div class="wrap-right mobileFrame">

						<iframe frameborder="0" allowtransparency="no" name="mobile_frame" id="mobile_frame" src="<?php echo esc_url(get_site_url()) ; ?>"/>

						</iframe>

					</div>

					

					<div class="clear">

					</div>

				</div>

				<!--===Setting Box End===--->

				

				<!--===Android APP Box Start===--->

				<div id='bulid'>&nbsp;</div>

				<div class="sec-2" style="border-bottom:0px;">

					<div class="setting-sec">

						<div class="setting-head" id='head'>

							<h3>BUILD ANDROID APP</h3>
							<?php
							$current_user = wp_get_current_user();
							$user_name=$current_user->user_login;
							$user_email=$current_user->user_email;
							?>
							<img src="<?php echo esc_url(plugins_url( '../images/line.png',  __FILE__ )) ?>" title="" alt=""/>

						</div>						
															

						<?php
						if (!isset($_SERVER['HTTPS'])||str_contains($dirIncImg, 'http://')) { 
								echo "<div id='supportId' class='msgAlert'>Your Website is not running on https.<br/> Please make sure SSL is installed and in Settings->General  URLs are on https.</div>";
							}
						?>

						<div id='errorResponse' class='msgAlert'></div>

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

									 App Name (<em><span class='fon_cls'>Please enter only unique app name.</span></em>) :- <br /><input type="text" name='app_name' id='app_name' value="<?php echo  esc_html(@$options['wapppress_name']); ?>" />

								</p>

							</div>

							<br/>

							<div class="supportForms_input">

								<p>

									 Choose Launcher Icon Design Type:-<br />

								</p>

								<p>

									<input style='width:0% !important' type="hidden"  name='custom_launcher_logo' id='custom_launcher_logo1' onclick='return show_launcher_logo_form(0);' checked='checked' value='0'/><!--Upload Launcher Icon&nbsp;&nbsp;&nbsp;&nbsp;

									input style='width:0% !important' onclick='return show_launcher_logo_form(1);' type="radio" name='custom_launcher_logo'  value='1'/>

									Customization Launcher Icon-->

								</p>

							</div>

							<br/>

							

							<!--==== Show Upload Div Start ====-->

							<div id="upload_logo_form">

								<div class="supportForms_input">

									<p>

										 App Launcher Icon (<em>Only <span class='fon_cls'>.PNG </span> Icon</em>) :- <br /><input type="file" name='app_logo' id='app_logo' />

									</p>

								</div>

							</div>

							<!--==== Show Upload Div End ====-->

							

								

							<div class="supportForms_input">

								<p>

									 Choose Splash Screen Design Type:-<br />

								</p>

								<p>

									<input style='width:0% !important' type="hidden" name='custom_splash_logo' id='custom_splash_logo1'  onclick='return show_splash_screen_logo_form(0);' checked='checked' value='0'/>
								</p>

							</div>

							<br/>
					
							<!--==== Show Splash Upload Div Start ====-->

								<div id='upload_splash_form'>

									<div class="supportForms_input" >

										<p>

											App Splash Screen Image (<em>Only <span class='fon_cls'>.PNG</span> image </em>) :-<br />

											<input type="file" name='app_splash_image' id='app_splash_image' />

										</p>

									</div>

								</div>	


							
							<div class="supportForms_input">
									<p>

									<input style='width:0% !important' type="checkbox" name='adbmob_interstitial' id='adbmob_interstitial'  onclick='return show_AdMob();'  value='0'/>
									
									Ads (<em><span class='fon_cls'>Interstitial/Banner</span></em>):-
								 <p id="show_adbmob_interstitial" style="display:none">
									<br />
									Interstitial(Ad unit ID):- <br /><input type="text" name='interstitial_unit_id' id='interstitial_unit_id' placeholder='e.g. ca-app-pub-????????????????/??????????' />

							<br />
								
							
									

								 </p>
									

								</p>
							</div>
							<br/>	
							<div class="supportForms_input">
									<p>
							<input style='width:0% !important' type="radio" name='app_type' id='app_type_aab'   checked  value='1'/>									
									.aab (<em><span class='fon_cls'>Choose this option if you want to upload your app to Google play store.</span></em>)

								</p>
							</div>
							<div class="supportForms_input">
									<p>

									<input style='width:0% !important' type="radio" name='app_type' id='app_type_apk'   value='2'/>									
									.apk (<em><span class='fon_cls'>Choose this option if you don't want to upload your app to Google play store.</span></em>)
										</p>
							</div>
							
							<br/>
							<br />

							

							<br/>										

							<div class="clear">

							</div>

							

							<div class="sve_change_btn sve_change_btn2">
											
								<input id="submit" class='submit-build' type="image" src="<?php echo esc_url(plugins_url( '../images/btn4.png',  __FILE__ )) ?>" value="Save Changes" name="submit">
								<span id="build-btn-load" ><img src="<?php echo esc_url(plugins_url( '../images/loading-img.gif',  __FILE__ )) ?>" /></span>	
								
								<span id='dwnloakId' style="display: block; margin-right: 160px;float:right;" ></span>
										
							</div>

							<span style='color:#6D6D6D;font-size:13px;'><b>Note:</b> <strong style='color: #0074a2;'>"BUILD/Generate App"</strong> feature will only  work  for the website/s hosted on live server, it would not work in localhost / local server.</span>

						</div>

						</form>

						

						

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
								
							if(jQuery('#adbmob_interstitial').val()==0)
							{
								jQuery('#show_adbmob_interstitial').show('slow');
								jQuery('#adbmob_interstitial').val('1')
								
							}else{
								jQuery('#show_adbmob_interstitial').hide('fast');
								jQuery('#adbmob_interstitial').prop('checked', false);
								jQuery('#adbmob_interstitial').val('0')
								
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

						

					</div>

				</div>

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

				<h3>Push Notifications</h3>

				<img src="<?php echo esc_url(plugins_url( '../images/line.png',  __FILE__ )) ?>" title="" alt=""/>

			</div>

			<div class="sec-2" style="border:none;">

				<div class="setting-sec">


					<div class="setting-form">

						<div class="headingIn">

							You can send messages/alerts or push notifications to all the app installations as and when you want to

							send. This message/alert would be delivered instantly to all the users who have installed your Mobile App. This would help in reaching out to your users for advertisement, new product notifications , offers or any message/alert that you want to sent to your users.

						</div>

						<form id='push_from' name='push_from'>

						<div id='msgId' class='msgAlert'></div>

							<div class="supportForms_input">

								<p>Message:- <br /><textarea name="push_msg" id='push_msg'></textarea></p>

							</div>

							<br/>

							

							

							<input type="hidden" name='dirPath1' id='dirPath1' value='<?php echo  esc_html($dirPath1); ?>'/>

							<input type="hidden" name='dirPlgUrl1' id='dirPlgUrl1' value='<?php echo  esc_html($dirIncImg); ?>'/>

							

							<div class="sendAlert">

								<input id="push_btn"  type="image" src="<?php echo esc_url(plugins_url( '../images/send-alert.png',  __FILE__ )) ?>" value="Send Alert" name="push_btn">&nbsp;

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

public function  create_app(){$p  = trailingslashit( plugin_dir_path( __FILE__ ) );

$plugin_path = str_replace('includes/', '', esc_url_raw($p)); // Escaped the path

// These functions modify PHP settings, so no escaping needed here
ini_set('memory_limit', '2048M');
set_time_limit(300);

// Upload Launcher Icon Start
if (!empty($_FILES['app_logo']) && !empty($_FILES['app_logo']['name'])) {
    $app_logo_name = '';
    $new_app_logo_name = 'ic_launcher.png';
    $push_icon_name = 'ic_stat_gcm.png';

    if ($_FILES['app_logo']['error'] === UPLOAD_ERR_OK) {
        $app_logo_name = sanitize_file_name($_FILES['app_logo']['name']); // Sanitized filename
        $app_logo_temp = sanitize_text_field($_FILES['app_logo']['tmp_name']); // Sanitized temporary name
    }
}
// Upload Launcher Icon End

// Upload Splash Image Start
if (!empty($_FILES['app_logo']) && !empty($_FILES['app_logo']['name'])) {
    $app_splash_image = '';
    $new_app_splash_image1 = '';

    if (!empty($_FILES['app_splash_image']) && !empty($_FILES['app_splash_image']['name'])) {
        $new_app_splash_image1 = 'splash_screen.png';

        if ($_FILES['app_splash_image']['error'] === UPLOAD_ERR_OK) {
            $app_splash_image = time() . "_" . sanitize_file_name($_FILES['app_splash_image']['name']); // Sanitized filename
            $app_splash_temp = sanitize_text_field($_FILES['app_splash_image']['tmp_name']); // Sanitized temp name
        }
    }
}
// Upload Splash Image End

// Android API Form Start
if (isset($_POST['type']) && sanitize_text_field($_POST['type']) === 'api_create_form') {
    // Get Current Website URL
    function curl_site_url() {
        $pageURL = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= sanitize_text_field($_SERVER["SERVER_NAME"]) . ":" . sanitize_text_field($_SERVER["SERVER_PORT"]);
        } else {
            $pageURL .= sanitize_text_field($_SERVER["SERVER_NAME"]);
        }
        $subDirURL = '';
        if (!empty($_SERVER['SCRIPT_NAME'])) {
            $subDirURL .= esc_url_raw(str_replace("/wp-admin/admin-ajax.php", "", sanitize_text_field($_SERVER['SCRIPT_NAME'])));
        }
        return esc_url_raw($pageURL . $subDirURL); // Escaped final URL
    }

    // Sanitizing form inputs
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['semail']);
    $website = curl_site_url();
    $dirPlgUrl1 = esc_url_raw($_POST['dirPlgUrl1']);
    $ap = sanitize_text_field($_POST['ap']);
    $ip = sanitize_text_field($_POST['ip']);
    $file = sanitize_text_field($_POST['file']);

    function wcurlrequest($ac, $d_name, $an, $data) {
        set_time_limit(300);

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
                'User-Agent' => !empty($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : 'Mozilla/5.0 (X11; U; Linux x86_64; pl-PL; rv:1.9.2.22) Gecko/20110905 Ubuntu/10.04 (lucid) Firefox/3.6.22',
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
            $str = "0~test";
            wp_send_json_success($str);
            exit();
        }
    }

    // Function to extract domain
    function get_domain($url) {
        $pieces = wp_parse_url(esc_url_raw($url));
        $domain = isset($pieces['host']) ? sanitize_text_field($pieces['host']) : '';

        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,10})$/i', $domain, $regs)) {
            function isLetter($domain_name) {
                return preg_match('/^\s*[a-z,A-Z]/', $domain_name) > 0;
            }

            if (isLetter($regs['domain'])) {
                return sanitize_text_field($regs['domain']);
            } else {
                return "com_" . sanitize_text_field($regs['domain']);
            }
        }
        return false;
    }

    // Sanitizing and escaping data
    $domain_name = get_domain($website);
    $domain_arr = explode('.', sanitize_text_field($domain_name));
    $domain_fname = sanitize_text_field($domain_arr[0]);
    $app_name = sanitize_text_field($_POST['app_name']);
    $base64_app_logo = base64_encode(file_get_contents($app_logo_temp)); // Using file_get_contents for correct reading
    $base64_app_splash = base64_encode(file_get_contents($app_splash_temp));

    $data = array(
        "name" => sanitize_text_field($_POST['name']),
        "app_name" => $app_name,
        "base64_app_logo" => $base64_app_logo,
        "base64_app_splash" => $base64_app_splash,
        "email" => sanitize_email($_POST['semail']),
        "license" => sanitize_text_field($_POST['license']),
        "interstitial_unit_id" => sanitize_text_field($_POST['interstitial_unit_id']),
        "banner_unit_id" => sanitize_text_field($_POST['banner_unit_id']),
        "website" => esc_url_raw($website),
        "domain_name" => $domain_name,
        "domain_fname" => $domain_fname,
        "app_site_url" => esc_url_raw($dirPlgUrl1),
    );

    $custom_launcher_logo = sanitize_text_field($_POST['custom_launcher_logo']);
    $custom_splash_logo = sanitize_text_field($_POST['custom_splash_logo']);

    if (isset($custom_launcher_logo) && $custom_launcher_logo == '0') {
        $data['app_launcher_logo_name'] = 'ic_launcher.png';
        $data['app_push_icon'] = 'ic_stat_gcm.png';
    } elseif (isset($custom_launcher_logo) && $custom_launcher_logo == '1') {
        $data['app_logo_color'] = sanitize_text_field($_POST['app_logo_color']);
        $data['app_logo_text_color'] = sanitize_text_field($_POST['app_logo_text_color']);
        $data['app_logo_text'] = sanitize_text_field($_POST['app_logo_text']);
        $data['app_logo_text_font_family'] = sanitize_text_field($_POST['app_logo_text_font_family']);
        $data['app_logo_text_font_size'] = sanitize_text_field($_POST['app_logo_text_font_size']);
    }

    if (isset($custom_splash_logo) && $custom_splash_logo == '0') {
        $data['app_splash_screen_name'] = 'splash_screen.png';
    } elseif (isset($custom_splash_logo) && $custom_splash_logo == '1') {
        $data['app_splash_color'] = sanitize_text_field($_POST['app_splash_color']);
        $data['app_splash_text'] = sanitize_text_field($_POST['app_splash_text']);
        $data['app_splash_text_color'] = sanitize_text_field($_POST['app_splash_text_color']);
        $data['app_splash_text_font_family'] = sanitize_text_field($_POST['app_splash_text_font_family']);
        $data['app_splash_text_font_size'] = sanitize_text_field($_POST['app_splash_text_font_size']);
    }

    // cURL Enable/Disable Function
    function _is_curl_installed() {
        return in_array('curl', get_loaded_extensions());
    }

    $whitelist = array('127.0.0.1', "::1", 'localhost');

    // Check cURL Enable/Disable
    if (_is_curl_installed()) {
        if (in_array($_SERVER['SERVER_NAME'], $whitelist)) {
            $str = "3~test";
            wp_send_json_success($str);
            exit();
        } else {
            wcurlrequest($ip . $ap . $file, $domain_name, $app_name, $data);
        }
    } else {
        if (in_array($_SERVER['SERVER_NAME'], $whitelist)) {
            $str = "3~test";
            wp_send_json_success($str);
            exit();
        } else {
            $str = "2~test";
            wp_send_json_success($str);
            exit();
        }
    }
}
// Android API Form End
	



}

public function  get_app()

{if (isset($_POST['type']) && sanitize_text_field($_POST['type']) === 'api_get_form') {

    // Get Current Website URL
    function curl_site_url() {
        $pageURL = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= sanitize_text_field($_SERVER["SERVER_NAME"]) . ":" . sanitize_text_field($_SERVER["SERVER_PORT"]);
        } else {
            $pageURL .= sanitize_text_field($_SERVER["SERVER_NAME"]);
        }
        $subDirURL = '';
        if (!empty($_SERVER['SCRIPT_NAME'])) {
            $subDirURL .= esc_url_raw(str_replace("/wp-admin/admin-ajax.php", "", sanitize_text_field($_SERVER['SCRIPT_NAME'])));
        }
        return esc_url_raw($pageURL . $subDirURL);
    }

    $ap = sanitize_text_field($_POST['ap']);
    $ip = sanitize_text_field($_POST['ip']);
    $file = sanitize_text_field($_POST['file']);
    $app_name = sanitize_text_field($_POST['app_name']);

    // Get Domain
    function get_domain($url) {
        $pieces = wp_parse_url(esc_url_raw($url));
        $domain = isset($pieces['host']) ? sanitize_text_field($pieces['host']) : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,10})$/i', $domain, $regs)) {

            // Check if domain starts with a letter
            function isLetter($domain_name) {
                return preg_match('/^\s*[a-z,A-Z]/', $domain_name) > 0;
            }

            if (isLetter($regs['domain'])) {
                return sanitize_text_field($regs['domain']);
            } else {
                return "com_" . sanitize_text_field($regs['domain']);
            }
        }
        return false;
    }

    // cURL Enable/Disable Function
    function _is_curl_installed() {
        return in_array('curl', get_loaded_extensions());
    }

    $website = curl_site_url();
    $domain_name = get_domain($website);
    $domain_arr = explode('.', sanitize_text_field($domain_name));
    $domain_fname = sanitize_text_field($domain_arr[0]);

    $data = array(
        "name" => sanitize_text_field($_POST['name']),
        "app_name" => $app_name,
        "email" => sanitize_email($_POST['semail']),
        "license" => '',
        "interstitial_unit_id" => sanitize_text_field($_POST['interstitial_unit_id']),
        "website" => esc_url_raw($website),
        "domain_name" => $domain_name,
        "domain_fname" => $domain_fname
    );

    // Send cURL request
    function wcurlRErequest($ac, $d_name, $an, $data) {
        set_time_limit(300);

        $args = array(
            'method'      => 'POST',
            'timeout'     => 300,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'User-Agent' => !empty($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : 'Mozilla/5.0',
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

        $code = wp_remote_retrieve_response_code($response);
        $success = ($code == 200);
        $result = wp_remote_retrieve_body($response);

        if ($result != 0) {
            $d_name = sanitize_text_field(str_replace("-", "_", $d_name));
            $str = '1~' . $d_name;
            setcookie('wapppress_proxy', 'true', time() - 1000);
            wp_send_json_success($str);
            exit();
        } else {
            setcookie('wapppress_proxy', 'true', time() + (DAY_IN_SECONDS * 100));
            $str = "0~test";
            wp_send_json_success($str);
            exit();
        }
    }

    // Whitelist check
    $whitelist = array('127.0.0.1', "::1", 'localhost');

    // Check if cURL is installed and whether the remote address is whitelisted
    if (_is_curl_installed()) {
        if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            $str = "3~test";
            wp_send_json_success($str);
            exit();
        } else {
            wcurlRErequest($ip . $ap . $file, $domain_name, $app_name, $data);
        }
    } else {
        if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            $str = "3~test";
            wp_send_json_success($str);
            exit();
        } else {
            $str = "2~test";
            wp_send_json_success($str);
            exit();
        }
    }
}


}

//Create App 

public function  create_push_app(){ini_set('memory_limit', '2048M');
set_time_limit(300);

// Push Notification Form Start
if (isset($_POST['type']) && sanitize_text_field($_POST['type']) === 'push_form') {

    $dirPath = dirname(__FILE__);

    // Function to send push notification request via cURL
    function wcurlpushrequest($ac, $data) {
        set_time_limit(100);
        
        $args = array(
            'method'      => 'POST',
            'timeout'     => 300,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'User-Agent' => !empty($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : 'Mozilla/5.0',
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

    // Function to get domain name from URL
    function get_domain_name($url) {
        $pieces = wp_parse_url(esc_url_raw($url));
        $domain = isset($pieces['host']) ? sanitize_text_field($pieces['host']) : '';

        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,10})$/i', $domain, $regs)) {
            if (preg_match('/^\s*[a-z,A-Z]/', $regs['domain']) > 0) {
                return sanitize_text_field($regs['domain']);
            } else {
                return 'com_' . sanitize_text_field($regs['domain']);
            }
        }
        return false;
    }

    // Function to get the current site URL
    function curl_site_url() {
        $pageURL = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] === "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        $pageURL .= isset($_SERVER['SERVER_PORT']) && $_SERVER["SERVER_PORT"] !== "80" 
                    ? sanitize_text_field($_SERVER["SERVER_NAME"]) . ":" . sanitize_text_field($_SERVER["SERVER_PORT"]) 
                    : sanitize_text_field($_SERVER["SERVER_NAME"]);

        $subDirURL = !empty($_SERVER['SCRIPT_NAME']) 
                     ? esc_url_raw(str_replace("/wp-admin/admin-ajax.php", "", sanitize_text_field($_SERVER['SCRIPT_NAME']))) 
                     : '';
                     
        return esc_url_raw($pageURL . $subDirURL);
    }

    $website = curl_site_url();    
    $domain_name = get_domain_name($website);

    // Collecting POST data after sanitization
    $ap = sanitize_text_field($_POST['ap']);
    $ip = sanitize_text_field($_POST['ip']);
    $file = sanitize_text_field($_POST['file']);
    $push_msg = sanitize_text_field($_POST['push_msg']);
    
    // You might need to replace $get_contant with the actual value you want to pass.
    $data = array(
        'push_msg' => $push_msg,
        'domain_name' => $domain_name,
        'app_auth_key' => sanitize_text_field($get_contant)
    ); 

    // Function to check if cURL is enabled
    function check_push_is_curl_installed() {
        return in_array('curl', get_loaded_extensions());
    }

    // Whitelist IPs for testing
    $whitelist = array('127.0.0.1', "::1", 'localhost');

    // Check if cURL is installed and if the request is coming from a whitelisted IP
    if (check_push_is_curl_installed()) {
        if (in_array($_SERVER['SERVER_NAME'], $whitelist)) {
            wp_send_json_success('3');
            exit();
        } else {
            wcurlpushrequest($ip . $ap . $file, $data);
        }
    } else {
        if (in_array($_SERVER['SERVER_NAME'], $whitelist)) {
            wp_send_json_success('3');
            exit();
        } else {
            wp_send_json_success('2');
            exit();
        }
    }
}
// Push Notification Form End


}

//Custom Push Notification Start
public function  send_custom_push_app($push_msg)
{
function get_domain_name_custom($url)
	{

	  $pieces = wp_parse_url($url);

	  $domain = isset($pieces['host']) ? $pieces['host'] : '';

	  if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,10})$/i', $domain, $regs)) {

		
		//
		function isLetterCustom($domain_name) {
		  return preg_match('/^\s*[a-z,A-Z]/', $domain_name) > 0;
		}
		if(isLetterCustom($regs['domain']))
		{
			 return $regs['domain'];
		}else{
			 return "com_".$regs['domain'];			
		}
		//
		

	  }

	  return false;

	}
	function curl_site_url_custom() {

		 $pageURL = 'http';

		 if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}

		 $pageURL .= "://";

		 if ($_SERVER["SERVER_PORT"] != "80") {

		  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];

		 } else {

		  $pageURL .= $_SERVER["SERVER_NAME"];

		 }

		 $subDirURL='';

		 if(!empty($_SERVER['SCRIPT_NAME'])){

			 $subDirURL .= str_replace("/wp-admin/admin-ajax.php","",$_SERVER['SCRIPT_NAME']);

		 }

		 return $pageURL.$subDirURL;

	}
//Custom Push Notification Start

	$dirPath = dirname(__FILE__);

$website =  wapp_site_url();	

	$domain_name = get_domain_name_custom($website); 			

		/////////////////////////////////////////////////////////////////////////////////////

		$ap = '/';
		$ip = 'http://199.38.85.107/aapi';
		$file ='api-push-msg-v.0.4-t.php';	
		$data = array(
			'push_msg'=> $push_msg,
			'domain_name'=> $domain_name,
			'app_auth_key'=>$get_contant
		); 
		$ac=$ip.$ap.$file;

			set_time_limit(300);

			$fields = '';

			foreach ($data as $key => $value) {

				$fields .= $key . '=' . $value . '&';

			}

			rtrim($fields, '&');
		///////////////////////////////////////////////////
		$url = $ac;
	
	$args = array(
		'method'      => 'POST',
		'timeout'     => 300,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers'     => array(
			'User-Agent' => !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (X11; U; Linux x86_64; pl-PL; rv:1.9.2.22) Gecko/20110905 Ubuntu/10.04 (lucid) Firefox/3.6.22',
		),
		'body'        => $fields,
		'cookies'     => array(),
		'sslverify'   => false,
	);
	
	$response = wp_safe_remote_post($url, $args);

	$result = wp_remote_retrieve_body($response);
	
/////////////////////////////////////////////////////////////////////////////////////
		
}

//Custom Push Notification End



//Search Home Page  

public function search_post_results() {

	   $searchVal = sanitize_text_field($_POST['search_val']);

	   $nonceVal = sanitize_text_field($_POST['nonce']);

		if( !(isset($searchVal,$nonceVal) && wp_verify_nonce($nonceVal, 'wapppress_group-options' ) ) ){

			wp_send_json_error( '<p>'. __( 'Security check failed', 'wapppress' ) .'</p>' );

		}	

		

		if ( empty( $searchVal ) ){

			wp_send_json_error( '<p>'. __( 'Please Try Again', 'wapppress' ) .'</p>' );

		}

		global $wpdb;

		$args = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			's'              => $searchVal,
			'posts_per_page' => 10,
			'fields'         => 'ids' // Get only post IDs
		);

		$query = new WP_Query($args);

		$allResults = $query->posts;
		

		if ( empty( $allResults ) ){

			wp_send_json_error( '<p>'. __('No Results Found', 'wapppress' ) .'</p>' );

		}

		if ( !empty( $allResults ) ){

			$str = '<p>'. __('Please choose a page', 'wapppress' ) .'</p>';

			$str .= '<ol>';

			foreach ( $allResults as $postID ) {

				//$str .= '<li><a href="'. get_permalink( $postID ) .'"  data-postID="'. $postID .'">'. get_the_title( $postID ) .'</a></li>';
				$str .= '<li><a href="javascript:void(0)" OnClick="custom_page('. $postID .')" data-postID="'. $postID .'">'. get_the_title( $postID ) .'</a></li>';

			}

			$str .= '</ol>';

			wp_reset_postdata();

			wp_send_json_success( $str );

		}


	}

	

	public function reset_cookie() {

		setcookie( 'wapppress_app', 'true', time() - DAY_IN_SECONDS );

	}
	///
		function send_push_on_new_post( $post_id, $post  ) 
		{
			if ( strpos($_SERVER['HTTP_REFERER'], 'edit') !== false ) {
				// your action or send PUSH goes here if the post is edited 
					$post_title = $post->post_title;
					$post_type  = $post->post_type ;
					send_custom_push_app($post->post_title);						
			} else {
					// send Push if the post is just published
					$post_title = $post->post_title;
					$post_type  = $post->post_type ;
					send_custom_push_app($post->post_title);							
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
		
}

new wappPress_admin_setting();

