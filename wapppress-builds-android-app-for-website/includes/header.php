<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<title>WappPress</title>
<script type="text/javascript">
jQuery(document).ready(function($){
    /* prepend menu icon */
    jQuery('#nav-wrap').prepend('<div id="menu-icon">Menu</div>');
    /* toggle nav */
    jQuery("#menu-icon").on("click", function(){
        jQuery("#nav").slideToggle();
        jQuery(this).toggleClass("active");
    });
});

jQuery(document).ready(function() {
    jQuery('.tabs .tab-links a').on('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');
        // Show/Hide Tabs
        jQuery('.tabs ' + currentAttrValue).show().siblings().hide();
        // Change/remove current tab to active
        jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
        e.preventDefault();
    });
});

jQuery(document).ready(function () {
    jQuery('#toggle-view span,#toggle-view h3').click(function () {
        var text = jQuery(this).siblings('div.panel');
        if (text.is(':hidden')) {
            text.slideDown('200');
            jQuery(this).siblings('span').html('<img src="<?php echo  esc_url(plugins_url( '../images/down_arrow.png',  __FILE__ )) ?>" alt="down-arrow"/> ');
        } else {
            text.slideUp('200');    
            jQuery(this).siblings('span').html('<img src="<?php echo esc_url(plugins_url( '../images/arrow.png',  __FILE__ )) ?>" alt="up-arrow"/> ');            
        }
    });
});
</script>
</head>
<body>

<style>
.preview__header { font-size:12px !important; height:40px !important; background-color:#262626 !important; z-index:100 !important; line-height:54px !important; margin-bottom:1px !important; }
@media (max-width: 568px) { .preview__envato-logo { padding:0 10px !important; } }
.preview__envato-logo { float:left !important; padding:0 20px !important; }
.preview__envato-logo a { display:inline-block !important; position:absolute !important; top:10px !important; text-indent:-9999px !important; height:18px !important; width:152px !important; background:url(https://public-assets.envato-static.com/assets/logos/envato_market-a5ace93f8482e885ae008eb481b9451d379599dfed24868e52b6b2d66f5cf633.svg) !important; background-size:152px 18px !important; }
.preview__actions { float:right !important; }
.preview__action--buy, .preview__action--close { display:inline-block !important; padding:0 20px !important; padding-top:9px !important; }
.e-btn--3d.-color-primary { -webkit-box-shadow:0 2px 0 #6f9a37 !important; box-shadow:0 2px 0 #6f9a37 !important; position:relative !important; }
.e-btn--3d, .-color-primary.e-btn--outline { background-color:#82b440 !important; color:white !important; border-radius:15px !important; }
.e-btn.-size-s, .-size-s.e-btn--3d, .-size-s.e-btn--outline, .e-btn, .e-btn--3d, .e-btn--outline { font-size:14px !important; padding:5px 20px !important; line-height:1.5 !important; }
</style>
<?php
// Generate a nonce for the admin page
$wapppress_tab_nonce = wp_create_nonce( 'wapppress_admin_tab' );

// Get the raw nonce from $_GET
$wapppress_raw_nonce = isset($_GET['_wpnonce'])
    ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) )
    : '';

// Verify the nonce
$wapppress_is_valid_nonce = ! empty( $wapppress_raw_nonce )
    && wp_verify_nonce( $wapppress_raw_nonce, 'wapppress_admin_tab' );

// Get current page safely
$wapppress_current_page = ( isset($_GET['page']) && $wapppress_is_valid_nonce )
    ? sanitize_text_field( wp_unslash( $_GET['page'] ) )
    : '';
?>

<div class="tab-h" style="display:block">
    <div class="logo">
        <img src="<?php echo esc_url( plugins_url( '../images/logo.png', __FILE__ ) ); ?>" alt="">
    </div>

    <button class="tablinks <?php echo ( $wapppress_current_page === 'wapppresssettings' ) ? ' active' : ''; ?>" 
        onclick="window.location.href='<?php echo esc_url( admin_url('admin.php?page=wapppresssettings&_wpnonce=' . $wapppress_tab_nonce) ); ?>';">
        Build Android App
    </button>

    <button class="tablinks <?php echo ( $wapppress_current_page === 'advancesettings' ) ? ' active' : ''; ?>" 
        onclick="window.location.href='<?php echo esc_url( admin_url('admin.php?page=advancesettings&_wpnonce=' . $wapppress_tab_nonce) ); ?>';">
       
      Advance Settings
    </button>

    <button class="tablinks <?php echo ( $wapppress_current_page === 'wapppresspush' ) ? ' active' : ''; ?>" 
        onclick="window.location.href='<?php echo esc_url( admin_url('admin.php?page=wapppresspush&_wpnonce=' . $wapppress_tab_nonce) ); ?>';">
        Push Notification <span>(Message)</span>
    </button>
	<button class="tablinks"
			onclick="window.open('https://iwapppress.wapppress.com/iosapp', '_blank', 'noopener');">
		 Build iOS App
	</button>
	<button class="tablinks"
			onclick="window.open('https://wapppress.zohodesk.in/portal', '_blank', 'noopener');">
		Help/Support
	</button>

</div>


