<?php
/**
 *
 * Plugin Name: Civist
 * Description: With Civist you create petitions directly in WordPress, raise funds and build strong supporter networks.
 * Version:     7.7.0
 * Author:      Civist
 * Author URI:  https://civist.com
 * License:     MIT
 * Text Domain: civist
 *
 * @package civist
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * The plugin's uninstall handler.
 */
function uninstall_civist() {
	delete_option( 'civist' );
}

register_uninstall_hook( __FILE__, 'uninstall_civist' );

/**
 * Instantiate the plugin class
 */
function run_civist() {
	$version = '7.7.0';
	$plugin_name = 'Civist';
	$plugin_slug = 'civist';
	$plugin_text_domain = 'civist';
	$plugin_file = __FILE__;
	$registration_url = 'https://registration.civist.cloud/';
	$geoip_url = 'https://geoip.civist.cloud/';
	$widget_supported_languages = json_decode( '["ca","cs","de","en","en-GB","en-US","es","eu","fr","hr","it","lt","nl","nl-NL-x-formal","nb","pl","pt","ro","sk","sv","tr","ko"]' );
	$enforce_https = true;
	if ( defined( 'CIVIST_ENFORCE_HTTPS' ) ) {
		$enforce_https = CIVIST_ENFORCE_HTTPS;
	}

	require_once( 'class-civist.php' );
	$plugin = new Civist( $version, $plugin_name, $plugin_slug, $plugin_text_domain, $plugin_file, $registration_url, $geoip_url, $widget_supported_languages, $enforce_https );
	$plugin->run();
}
run_civist();
