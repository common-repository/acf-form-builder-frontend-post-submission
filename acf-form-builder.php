<?php

/**
 * Plugin Name: ACF Form Builder
 * Plugin URI:  http://catsplugins.com
 * Description: Create stunning form like subscription form, checkout form, contact form,..etc with ACF
 * Version:     1.0.0
 * Author:      Cat's Plugins
 * Author URI:  http://catsplugins.com
 * License: GNU General Public License, version 3 (GPL-3.0)
 * License URI: http://www.gnu.org/copyleft/gpl.html
 * Text Domain: acf-form-buider
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-acf-form-builder-activator.php
 */
function activate_acf_form_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acf-form-builder-activator.php';
	Acf_Form_Builder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-acf-form-builder-deactivator.php
 */
function deactivate_acf_form_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acf-form-builder-deactivator.php';
	Acf_Form_Builder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_acf_form_builder' );
register_deactivation_hook( __FILE__, 'deactivate_acf_form_builder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-acf-form-builder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_acf_form_builder() {

	// add field in to acf field admin
	$dir = plugin_dir_path( __FILE__ );
	include_once( $dir . 'includes/settings/class-acf-form-builder-settings.php' );
	include_once( $dir . 'includes/default-actions/acf-form-builder-default-actions.php' );

	$plugin = new Acf_Form_Builder();
	$plugin->run();

}
run_acf_form_builder();