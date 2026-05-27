<?php
/**
 * INSTANTAPPY – Manifest & Service Worker functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* ============================================================
   MANIFEST
   ============================================================ */

/**
 * Manifest filename (multisite-aware)
 */
function INSTANTAPPY_PWA_manifest_filename() {
	return 'INSTANTAPPY-manifest' . INSTANTAPPY_multisite_handler() . '.json';
}

/**
 * Manifest filename / absolute path / URL
 *
 * @param string $arg  'filename' | 'abs' | 'src'
 */
function INSTANTAPPY_manifest( $arg = 'src' ) {
	$manifest = INSTANTAPPY_PWA_manifest_filename();

	switch ( $arg ) {
		case 'filename':
			return $manifest;
		case 'abs':
			return trailingslashit( ABSPATH ) . $manifest;
		case 'src':
		default:
			return trailingslashit( network_site_url() ) . $manifest;
	}
}

/**
 * Generate / write the manifest JSON to the site root.
 */
function INSTANTAPPY_generate_pwa_manifest() {

	$settings = INSTANTAPPY_grab_pwa_basic_settings();

	$manifest                    = array();
	$manifest['name']            = $settings['app_name'];
	$manifest['short_name']      = $settings['app_short_name'];

	if ( isset( $settings['description'] ) && ! empty( $settings['description'] ) ) {
		$manifest['description'] = $settings['description'];
	}

	$manifest['icons']            = INSTANTAPPY_get_pwa_icons();
	$manifest['background_color'] = $settings['background_color'];
	$manifest['theme_color']      = $settings['theme_color'];
	$manifest['display']          = 'standalone';
	$manifest['orientation']      = INSTANTAPPY_get_pwa_orientation();
	$manifest['start_url']        = INSTANTAPPY_get_pwa_start_url( true );
	$manifest['scope']            = INSTANTAPPY_get_pwa_scope();

	$manifest = apply_filters( 'INSTANTAPPY_manifest', $manifest );

	$json = wp_json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

	if ( ! INSTANTAPPY_put_pwa_contents( INSTANTAPPY_manifest( 'abs' ), $json ) ) {
		return false;
	}

	update_option( 'instantappy_pwa_manifest_version', time() );

	return true;
}

/**
 * Inject <link rel="manifest"> and theme-color meta into <head>
 */
function INSTANTAPPY_add_manifest_to_header() {

	$tags = '<!-- This manifest is added by INSTANTAPPY - Progressive Web Apps(PWA) Plugin For WordPress -->' . PHP_EOL;

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

	$tags .= apply_filters( 'INSTANTAPPY_header_tags', '' );
	$tags .= '<!-- / INSTANTAPPY PWA -->' . PHP_EOL;

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
 * Delete the manifest file
 */
function INSTANTAPPY_delete_pwa_manifest() {
	return INSTANTAPPY_delete( INSTANTAPPY_manifest( 'abs' ) );
}

/**
 * Build the icons array for the manifest
 */
function INSTANTAPPY_get_pwa_icons() {

	$path_src = INSTANTAPPY_PATH_SRC . 'public/images/';

	$sizes = array(
		'16x16', '32x32', '48x48', '70x70', '72x72',
		'96x96', '144x144', '150x150', '180x180', '192x192',
		'310x150', '310x310', '512x512',
	);

	$icons = array();
	foreach ( $sizes as $size ) {
		$icons[] = array(
			'src'   => $path_src . $size . '.png',
			'sizes' => $size,
			'type'  => 'image/png',
		);
	}

	return $icons;
}

/**
 * Get the PWA scope (path segment of the site URL)
 */
function INSTANTAPPY_get_pwa_scope() {
	return wp_parse_url( trailingslashit( get_bloginfo( 'wpurl' ) ), PHP_URL_PATH );
}

/**
 * Get orientation string from settings
 */
function INSTANTAPPY_get_pwa_orientation() {

	$settings    = INSTANTAPPY_grab_pwa_basic_settings();
	$orientation = isset( $settings['orientation'] ) ? (int) $settings['orientation'] : 0;

	switch ( $orientation ) {
		case 1:
			return 'portrait';
		case 2:
			return 'landscape';
		case 0:
		default:
			return 'any';
	}
}

/* ============================================================
   SERVICE WORKER
   ============================================================ */

/**
 * Service-worker filename (multisite-aware)
 */
function INSTANTAPPY_PWA_service_worker_filename() {
	return apply_filters(
		'INSTANTAPPY_PWA_service_worker_filename',
		'sw' . INSTANTAPPY_multisite_handler() . '.js'
	);
}

/**
 * Service-worker filename / absolute path / URL path
 *
 * @param string $arg  'filename' | 'abs' | 'src'
 */
function INSTANTAPPY_PWA_service_worker( $arg = 'src' ) {
	$sw_filename = INSTANTAPPY_PWA_service_worker_filename();

	switch ( $arg ) {
		case 'filename':
			return $sw_filename;
		case 'abs':
			return trailingslashit( ABSPATH ) . $sw_filename;
		case 'src':
		default:
			return wp_parse_url(
				trailingslashit( network_site_url() ) . $sw_filename,
				PHP_URL_PATH
			);
	}
}

/**
 * Generate / write the service worker file.
 */
function INSTANTAPPY_generate_pwa_sw() {

	$sw = INSTANTAPPY_pwa_sw_template();

	if ( ! INSTANTAPPY_put_pwa_contents( INSTANTAPPY_PWA_service_worker( 'abs' ), $sw ) ) {
		return false;
	}

	return true;
}

/**
 * Service-worker JS template
 */
function INSTANTAPPY_pwa_sw_template() {
	ob_start();
	?>
'use strict';

/**
 * Service Worker – <?php echo esc_js( INSTANTAPPY_get_pwa_start_url() ); ?>

 */

const cacheName      = '<?php echo esc_js( wp_parse_url( get_bloginfo( 'wpurl' ), PHP_URL_HOST ) . '-INSTANTAPPY-' . INSTANTAPPY_VERSION ); ?>';
const filesToCache   = [];
const neverCacheUrls = [];
const startPage      = '<?php echo esc_js( INSTANTAPPY_get_pwa_start_url() ); ?>';
const offlinePage    = '';

self.addEventListener('install', function(e) {
	console.log('INSTANTAPPY service worker installation');
	e.waitUntil(
		caches.open(cacheName).then(function(cache) {
			console.log('INSTANTAPPY service worker caching dependencies');
			filesToCache.map(function(url) {
				return cache.add(url).catch(function(reason) {
					return console.log('INSTANTAPPY: ' + String(reason) + ' ' + url);
				});
			});
		})
	);
});

self.addEventListener('fetch', function(e) {

	if (!neverCacheUrls.every(checkNeverCacheList, e.request.url)) {
		console.log('INSTANTAPPY: Current request is excluded from cache.');
		return;
	}

	if (!e.request.url.match(/^(http|https):\/\//i)) return;

	if (new URL(e.request.url).origin !== location.origin) return;

	if (e.request.method !== 'GET') {
		e.respondWith(
			fetch(e.request).catch(function() {
				return caches.match(offlinePage);
			})
		);
		return;
	}

	if (e.request.mode === 'navigate' && navigator.onLine) {
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

self.addEventListener('activate', function(e) {
	console.log('INSTANTAPPY service worker activation');
	e.waitUntil(
		caches.keys().then(function(keyList) {
			return Promise.all(keyList.map(function(key) {
				if (key !== cacheName) {
					console.log('INSTANTAPPY old cache removed', key);
					return caches.delete(key);
				}
			}));
		})
	);
	return self.clients.claim();
});

function checkNeverCacheList(url) {
	if (this.match(url)) { return false; }
	return true;
}
	<?php
	return apply_filters( 'INSTANTAPPY_pwa_sw_template', ob_get_clean() );
}

/**
 * Delete the service-worker file
 */
function INSTANTAPPY_delete_sw() {
	return INSTANTAPPY_delete( INSTANTAPPY_PWA_service_worker( 'abs' ) );
}

/* ============================================================
   SERVICE WORKER REGISTRATION (front-end)

   BUG FIX: Original code used wp_enqueue_script() with the SW
   file as the src, which outputs <script src="sw.js">. That is
   wrong — the browser just executes it as a regular script and
   the self/ServiceWorkerGlobalScope APIs are unavailable.
   The SW must be registered via navigator.serviceWorker.register().
   ============================================================ */

function INSTANTAPPY_register_sw() {

	if ( is_admin() ) {
		return;
	}

	$sw_src = trailingslashit( network_site_url() ) . INSTANTAPPY_PWA_service_worker_filename();
	$scope  = INSTANTAPPY_get_pwa_scope();

	wp_register_script( 'instantappy-register-sw', '', array(), INSTANTAPPY_VERSION, true );
	wp_enqueue_script( 'instantappy-register-sw' );
	wp_add_inline_script(
		'instantappy-register-sw',
		"if ('serviceWorker' in navigator) {
			window.addEventListener('load', function() {
				navigator.serviceWorker.register(
					'" . esc_js( $sw_src ) . "',
					{ scope: '" . esc_js( $scope ) . "' }
				).then(function(reg) {
					console.log('INSTANTAPPY SW registered, scope:', reg.scope);
				}).catch(function(err) {
					console.warn('INSTANTAPPY SW registration failed:', err);
				});
			});
		}"
	);
}
add_action( 'wp_enqueue_scripts', 'INSTANTAPPY_register_sw' );

/* ============================================================
   PWA INSTALL BUTTON
   ============================================================ */

add_action( 'wp_footer', 'instantappy_install_button_settings' );
function instantappy_install_button_settings() {
	?>
	<div id="instantappy-installer" style="display:none;">
		<button id="instantappy-install-btn">📲 Install App</button>
	</div>
	<?php
}

add_action( 'wp_head', function () {
	?>
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
	<?php
} );

add_action( 'wp_footer', function () {
	?>
<script>
(function() {
	var deferredPrompt = null;
	var installBox     = document.getElementById('instantappy-installer');
	var installBtn     = document.getElementById('instantappy-install-btn');

	window.addEventListener('beforeinstallprompt', function(e) {
		e.preventDefault();
		deferredPrompt = e;
		if (installBox) installBox.style.display = 'block';
	});

	if (installBtn) {
		installBtn.addEventListener('click', function() {
			if (!deferredPrompt) return;
			deferredPrompt.prompt();
			deferredPrompt.userChoice.then(function(result) {
				console.log('PWA install outcome:', result.outcome);
				deferredPrompt = null;
				if (installBox) installBox.style.display = 'none';
			});
		});
	}

	window.addEventListener('appinstalled', function() {
		console.log('PWA installed');
		if (installBox) installBox.style.display = 'none';
	});
})();
</script>
	<?php
} );

/* ============================================================
   OFFLINE PAGE – cache images
   ============================================================ */

function INSTANTAPPY_pwa_offline_page_images( $files_cache ) {

	$settings = INSTANTAPPY_grab_pwa_basic_settings();
	$post     = get_post( $settings['offline_page'] );

	if ( $post === null ) {
		return $files_cache;
	}

	preg_match_all( '/<img[^>]+src="([^">]+)"/', $post->post_content, $matches );

	if ( ! empty( $matches[1] ) ) {
		return INSTANTAPPY_pwa_httpsify(
			$files_cache . ", '" . implode( "', '", $matches[1] ) . "'"
		);
	}

	return $files_cache;
}
add_filter( 'INSTANTAPPY_pwa_sw_files_to_cache', 'INSTANTAPPY_pwa_offline_page_images' );
