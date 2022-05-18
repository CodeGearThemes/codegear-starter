<?php
/**
 *
 * Plugin Name:       Codegear Starter
 * Plugin URI:        https://codegearthemes.com/product/codegear-starter
 * Description:       CodeGearThemes starter sites.
 * Version:           1.0.1
 * Author:            CodeGearThemes
 * Author URI:        https://codegearthemes.com
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       codegear-starter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CODEGEAR_STARTER_VERSION', '1.0.0' );
define( 'CODEGEAR_STARTER_URL', plugin_dir_url( __FILE__ ) );
define( 'CODEGEAR_STARTER_PATH', plugin_dir_path( __FILE__ ) );


/**
 * Plugin activation.
 * This action is documented in includes/class-codegear-starter-activator.php
 */
function activate_codegear_starter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-codegear-starter-activator.php';
	Codegear_Starter_Activator::activate();
}

/**
 * Plugin deactivation.
 * This action is documented in includes/class-codegear-starter-deactivator.php
 */
function deactivate_codegear_starter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-codegear-starter-deactivator.php';
	Codegear_Starter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_codegear_starter' );
register_deactivation_hook( __FILE__, 'deactivate_codegear_ctarter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-codegear-starter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_codegear_starter() {

	$plugin = new Codegear_Starter();
	$plugin->run();

}
run_codegear_starter();
