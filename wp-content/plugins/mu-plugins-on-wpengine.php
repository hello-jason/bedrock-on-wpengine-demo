<?php
/**
 * Plugin Name: Allow mu-plugins on WP Engine
 * Plugin URI: https://
 * Description: Fill this in later
 * Version: 1.0.0
 * Author: Jason Cross
 * Author URI: https://hellojason.net
 * License: Whatever you want
 */

// Call functions
// require( ABSPATH . WPINC . '/plugin.php' );
custom_wp_plugin_directory_constants();
wp_get_custom_mu_plugins();
// get_custom_mu_plugins();
// define('WPMU_CUSTOM_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins-custom');

// Set globals
function custom_wp_plugin_directory_constants() {
	if ( !defined('WPMU_CUSTOM_PLUGIN_DIR') )
		define( 'WPMU_CUSTOM_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins-custom' ); // full path, no trailing slash
}

/**
 * Retrieve an array of must-use plugin files.
 *
 * The default directory is wp-content/mu-plugins. To change the default
 * directory manually, define `WPMU_CUSTOM_PLUGIN_DIR` and `WPMU_PLUGIN_URL`
 * in wp-config.php.
 *
 * @since 3.0.0
 * @access private
 *
 * @return array Files to include.
 */
function wp_get_custom_mu_plugins() {
	$custom_mu_plugins = array();
	if ( !is_dir( WPMU_CUSTOM_PLUGIN_DIR ) )
		return $custom_mu_plugins;
	if ( ! $dh = opendir( WPMU_CUSTOM_PLUGIN_DIR ) )
		return $custom_mu_plugins;
	while ( ( $plugin = readdir( $dh ) ) !== false ) {
		if ( substr( $plugin, -4 ) == '.php' )
			$custom_mu_plugins[] = WPMU_CUSTOM_PLUGIN_DIR . '/' . $plugin;
	}
	closedir( $dh );
	sort( $custom_mu_plugins );

	return $custom_mu_plugins;
}

// Load must-use plugins.
foreach ( wp_get_custom_mu_plugins() as $custom_mu_plugin ) {
	include_once( $custom_mu_plugin );
}
unset( $custom_mu_plugin );

// Load network activated plugins.
if ( is_multisite() ) {
	foreach ( wp_get_active_network_plugins() as $network_plugin ) {
		wp_register_plugin_realpath( $network_plugin );
		include_once( $network_plugin );
	}
	unset( $network_plugin );
}
