<?php
/**
 * INSTANTAPPY-Manifest related functions
 */
// Exit if file accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * return service worker manifest file
 */
 function INSTANTAPPY_PWA_manifest_filename() {
	return 'INSTANTAPPY-manifest' . INSTANTAPPY_multisite_handler() . '.json';
}
/**
 * Manifest filename, absolute path and link
 */
function INSTANTAPPY_manifest( $arg = 'src' ) 
{
	
	$manifest = INSTANTAPPY_PWA_manifest_filename();
	
	switch( $arg ) {		
		case 'filename': 
			return $manifest;// Set Name for Manifest file
			break;				
		case 'abs':
			return trailingslashit( ABSPATH ) . $manifest; // Set Absolute path to manifest
			break;		
		case 'src':
		default:
			return trailingslashit( network_site_url() ) . $manifest; // src/link to manifest
			break;
	}
}

/**
 * Generate/write manifest into root diretory of the website
 */
function INSTANTAPPY_generate_pwa_manifest() 
{
	
	// Get PWA Settings
	$settings = INSTANTAPPY_grab_pwa_basic_settings();	
	$manifest 						= array();
	$manifest['name']				= $settings['app_name']; //PWA Name
	$manifest['short_name']			= $settings['app_short_name']; // PWA Short Name
	
	
	if ( isset( $settings['description'] ) && ! empty( $settings['description'] ) ) {
		$manifest['description'] 	= $settings['description']; // PWA Description
	}
	
	$manifest['icons']				= INSTANTAPPY_get_pwa_icons(); //Get PWA ICONS
	$manifest['background_color']	= $settings['background_color'];
	$manifest['theme_color']		= $settings['theme_color'];
	$manifest['display']			= 'standalone';
	$manifest['orientation']		= INSTANTAPPY_get_pwa_orientation(); // Get Orientation for PWA
	$manifest['start_url']			= INSTANTAPPY_get_pwa_start_url( true ); // Get PWA Start Page URL
	$manifest['scope']				= INSTANTAPPY_get_pwa_scope(); // Get Scope
	
	
	$manifest = apply_filters( 'INSTANTAPPY_manifest', $manifest ); // Apply above Filters to the manifest.
	 
	INSTANTAPPY_delete_pwa_manifest(); // Delete PWA manifest if it already exists.
	
	// Write/Create the manfiest to root directory.
	if ( ! INSTANTAPPY_put_pwa_contents( INSTANTAPPY_manifest( 'abs' ), wp_json_encode( $manifest ) ) ) {
		return false;
	}
	
	return true;
}

/**
 * Add manifest to header section i.e head
 *
 */
function INSTANTAPPY_add_manifest_to_header() {

	$tags  = '<!-- This manifest is added by INSTANTAPPY - Progressive Web Apps(PWA) Plugin For WordPress -->' . PHP_EOL;

	$manifest_path = wp_parse_url( INSTANTAPPY_manifest( 'src' ), PHP_URL_PATH );
	$version       = get_option( 'instantappy_pwa_manifest_version', '1' );
	if ( $manifest_path ) {
		$tags .= sprintf(
        '<link rel="manifest" href="%s">%s',
        esc_url( add_query_arg( 'v', $version, $manifest_path ) ),
        PHP_EOL
    );
	}

	if ( apply_filters( 'INSTANTAPPY_add_pwa_theme_color', true ) ) {

		$settings = INSTANTAPPY_grab_pwa_basic_settings();

		if ( ! empty( $settings['theme_color'] ) ) {
			$tags .= '<meta name="theme-color" content="' . esc_attr( $settings['theme_color'] ) . '">' . PHP_EOL;
		}
	}

	$tags  = apply_filters( 'INSTANTAPPY_header_tags', $tags );

	$tags .= '<!-- / INSTANTAPPY PWA -->' . PHP_EOL;

	// Allow only safe HTML tags
	echo wp_kses(
		$tags,
		array(
			'link' => array(
				'rel'  => true,
				'href' => true,
			),
			'meta' => array(
				'name'    => true,
				'content' => true,
			),
		)
	);
}

add_action( 'wp_head', 'INSTANTAPPY_add_manifest_to_header', 0 );

/**
 * Delete PWA manifest
 */
function INSTANTAPPY_delete_pwa_manifest() {
	return INSTANTAPPY_delete( INSTANTAPPY_manifest( 'abs' ) );
}

/**
 * Get PWA Icons
 */
function INSTANTAPPY_get_pwa_icons() 
{
		$path_src  =  INSTANTAPPY_PATH_SRC . 'public/images/';									
	
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'16x16.png',
							'sizes'	=> '16x16', 
							'type'	=> 'image/png', 
						);
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'32x32.png',
							'sizes'	=> '32x32', 
							'type'	=> 'image/png',
						);
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'48x48.png',
							'sizes'	=> '48x48', 
							'type'	=> 'image/png', 
						);
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'70x70.png',
							'sizes'	=> '70x70', 
							'type'	=> 'image/png', 
						);
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'72x72.png',
							'sizes'	=> '72x72', 
							'type'	=> 'image/png', 
						);
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'96x96.png',
							'sizes'	=> '96x96', 
							'type'	=> 'image/png',
						);
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'144x144.png',
							'sizes'	=> '144x144', 
							'type'	=> 'image/png', 
						);
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'150x150.png',
							'sizes'	=> '150x150', 
							'type'	=> 'image/png', 
						);
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'180x180.png',
							'sizes'	=> '180x180', 
							'type'	=> 'image/png', 
						);
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'192x192.png',
							'sizes'	=> '192x192', 
							'type'	=> 'image/png', 
						);
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'310x150.png',
							'sizes'	=> '310x150', 
							'type'	=> 'image/png', 
						);
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'310x310.png',
							'sizes'	=> '310x310', 
							'type'	=> 'image/png', 
						);
	$pwa_icons_array[] = array(
							'src' 	=> $path_src.'512x512.png',
							'sizes'	=> '512x512', 
							'type'	=> 'image/png', 
						);
	

	return $pwa_icons_array;
}

/**
 ** Get navigation scope of PWA
 */
function INSTANTAPPY_get_pwa_scope() 
{
	return wp_parse_url( trailingslashit( get_bloginfo( 'wpurl' ) ), PHP_URL_PATH );
}

/**
 ** Get orientation of PWA
 */
function INSTANTAPPY_get_pwa_orientation() 
{
	$settings = INSTANTAPPY_grab_pwa_basic_settings(); // Get PWA Settings
	
	$orientation = isset( $settings['orientation'] ) ? $settings['orientation'] : 0;
	
	switch ( $orientation ) {
		
		case 0:
			return 'any';
			break;
			
		case 1:
			return 'portrait';
			break;
			
		case 2:
			return 'landscape';
			break;
			
		default: 
			return 'any';
	}
}

/**
 * INSTANTAPPY-Service worker related functions 
 *
*/

/**
 * return service worker  file name sw.js
 */
 function INSTANTAPPY_PWA_service_worker_filename() {
    return apply_filters( 'INSTANTAPPY_PWA_service_worker_filename', 'sw' . INSTANTAPPY_multisite_handler() . '.js' );
}
/**
 *  Handle Service worker file
 */
function INSTANTAPPY_PWA_service_worker( $arg = 'src' )
 {
	
	$sw_filename = INSTANTAPPY_PWA_service_worker_filename();
		//return  network_site_url().'/'. $sw_filename;
	switch( $arg ) {
		
		// Name of SW file
		case 'filename':
			return $sw_filename;
			break;
		
		// Absolute path to SW, i.e root folder	patth
		case 'abs':
			return trailingslashit( ABSPATH ) . $sw_filename;
			break;
		
		// SRC/Link to SW
		case 'src':
		default:
			return wp_parse_url( trailingslashit( network_site_url() ) . $sw_filename, PHP_URL_PATH );
			break;
	}
}

/**
 * Generate/write service worker  in sw.js
 */
function INSTANTAPPY_generate_pwa_sw()
 {
	
	$settings = INSTANTAPPY_grab_pwa_basic_settings(); // Get PWA Settings
	
		$sw = INSTANTAPPY_pwa_sw_template(); // Get the SW tempalte
	
		INSTANTAPPY_delete_sw(); // Delete service worker file if it already exists
	
	if ( ! INSTANTAPPY_put_pwa_contents( INSTANTAPPY_PWA_service_worker( 'abs' ), $sw ) ) {
		return false;
	}
	
	return true;
}

/**
 * PWA Service Worker Tempalte
 */
function INSTANTAPPY_pwa_sw_template() {
	
	$settings = INSTANTAPPY_grab_pwa_basic_settings(); // Get PWA Settings
		// Start output buffer.
	ob_start();  ?>
'use strict';

/**
**
 * Service Worker file of <?php echo esc_html(INSTANTAPPY_get_pwa_start_url()); ?>
 **
  */
 
const cacheName = '<?php echo esc_html(wp_parse_url( get_bloginfo( 'wpurl' ), PHP_URL_HOST ) . '-INSTANTAPPY-' . INSTANTAPPY_VERSION); ?>';
const filesToCache = [];
const neverCacheUrls = [];
const startPage = '<?php echo esc_html(INSTANTAPPY_get_pwa_start_url()); ?>';
const offlinePage = '';
// Install Progressive Web App
self.addEventListener('install', function(e) {
	console.log('INSTANTAPPY service worker installation');
	e.waitUntil(
		caches.open(cacheName).then(function(cache) {
			console.log('INSTANTAPPY service worker caching dependencies');
			filesToCache.map(function(url) {
				return cache.add(url).catch(function (reason) {
					return console.log('INSTANTAPPY: ' + String(reason) + ' ' + url);
				});
			});
		})
	);
});

// Fetch Progressive Web App
self.addEventListener('fetch', function(e) {
	
	// Return if the current request url is in the never cache list
	if ( ! neverCacheUrls.every(checkNeverCacheList, e.request.url) ) {
	  console.log( 'INSTANTAPPY: Current request is excluded from cache.' );
	  return;
	}
	
	// Return if request url protocal isn't http or https
	if ( ! e.request.url.match(/^(http|https):\/\//i) )
		return;
	
	// Return if request url is from an external domain.
	if ( new URL(e.request.url).origin !== location.origin )
		return;
	
	// For POST requests, do not use the cache. Serve offline page if offline.
	if ( e.request.method !== 'GET' ) {
		e.respondWith(
			fetch(e.request).catch( function() {
				return caches.match(offlinePage);
			})
		);
		return;
	}
	
	// Revving strategy
	if ( e.request.mode === 'navigate' && navigator.onLine ) {
		e.respondWith(
			fetch(e.request).then(function(response) {
				return caches.open(cacheName).then(function(cache) {
					cache.put(e.request, response.clone());
					return response;
				});  
			})
		);
		return;
	}

	e.respondWith(
		caches.match(e.request).then(function(response) {
			return response || fetch(e.request).then(function(response) {
				return caches.open(cacheName).then(function(cache) {
					cache.put(e.request, response.clone());
					return response;
				});  
			});
		}).catch(function() {
			return caches.match(offlinePage);
		})
	);
});
// Activate Progressive Web App
self.addEventListener('activate', function(e) {
	console.log('INSTANTAPPY service worker activation');
	e.waitUntil(
		caches.keys().then(function(keyList) {
			return Promise.all(keyList.map(function(key) {
				if ( key !== cacheName ) {
					console.log('INSTANTAPPY old cache removed', key);
					return caches.delete(key);
				}
			}));
		})
	);
	return self.clients.claim();
});


// Check if current in the neverCacheUrls list
function checkNeverCacheList(url) {
	if ( this.match(url) ) {
		return false;
	}
	return true;
}
<?php return apply_filters( 'INSTANTAPPY_pwa_sw_template', ob_get_clean() );
}

/* PWA installation button settings */
add_action('wp_footer', 'instantappy_install_button_settings');
function instantappy_install_button_settings()
{
?>
	<div id="instantappy-installer" style="display:none;">
		<button id="instantappy-install-btn">📲 Install App</button>
	</div>
<?php
};
add_action( 'wp_head', function () { ?>
<style>
#instantappy-installer {
	position: fixed;
	bottom: 20px;
	right: 20px;
	z-index: 9999;
}

#instantappy-install-btn {
	background: #2563eb;
	color: #fff;
	border: none;
	border-radius: 8px;
	padding: 12px 18px;
	font-size: 15px;
	font-weight: 600;
	cursor: pointer;
	box-shadow: 0 10px 25px rgba(0,0,0,.2);
	transition: all .2s ease;
}

#instantappy-install-btn:hover {
	background: #1d4ed8;
	transform: translateY(-2px);
}
</style>
<?php } );
add_action( 'wp_footer', function () { ?>
<script>
let deferredPrompt = null;
const installBox = document.getElementById('instantappy-installer');
const installBtn = document.getElementById('instantappy-install-btn');

// Capture install prompt
window.addEventListener('beforeinstallprompt', (e) => {
	e.preventDefault(); // Stop Chrome default banner
	deferredPrompt = e;
	installBox.style.display = 'block';
});

// Button click → show install dialog
installBtn?.addEventListener('click', async () => {
	if (!deferredPrompt) return;

	deferredPrompt.prompt();
	const { outcome } = await deferredPrompt.userChoice;

	console.log('PWA install outcome:', outcome);

	deferredPrompt = null;
	installBox.style.display = 'none';
});

// Hide button after install
window.addEventListener('appinstalled', () => {
	console.log('PWA installed');
	installBox.style.display = 'none';
});
</script>
<?php } );

/**
 * Register service worker
 *
 * @from https://developers.google.com/web/fundamentals/primers/service-workers/registration#conclusion
 * 
 */
function INSTANTAPPY_register_sw() {
	

	wp_enqueue_script( 'intantappy-register-sw', site_url( 'sw.js' ), array(), INSTANTAPPY_VERSION, true );

	

}
add_action( 'wp_enqueue_scripts', 'INSTANTAPPY_register_sw' );
/**
 * Delete  PWA Service Worker
 */
function INSTANTAPPY_delete_sw() 
{
	return INSTANTAPPY_delete( INSTANTAPPY_PWA_service_worker( 'abs' ) );
}

/**
 * Addition of  images from offline page to filesToCache
 * 
 */
function INSTANTAPPY_pwa_offline_page_images( $files_cache ) {
	
	$settings = INSTANTAPPY_grab_pwa_basic_settings(); // Get PWA Settings
	
	$post = get_post( $settings['offline_page'] ); // Retrieve the data/post
	
	if( $post === NULL ) {
		return $files_cache; // Return if the offline page is default
	}
	
	// Check/Match all images
	preg_match_all( '/<img[^>]+src="([^">]+)"/', $post->post_content, $matches );
	
	// $matches[1] will contain all the src's
	if( ! empty( $matches[1] ) ) {
		return INSTANTAPPY_pwa_httpsify( $files_cache . ', \'' . implode( '\', \'', $matches[1] ) . '\'' );
	}
	
	return $files_cache;
}
add_filter( 'INSTANTAPPY_pwa_sw_files_to_cache', 'INSTANTAPPY_pwa_offline_page_images' );