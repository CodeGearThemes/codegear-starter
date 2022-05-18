<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://codegearthemes.com
 * @since      1.0.0
 *
 * @package    Codegear_Starter
 * @subpackage Codegear_Starter/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Codegear_Starter
 * @subpackage Codegear_Starter/includes
 * @author     CodeGearThemes <info@codegearthemes.com>
 */
class Codegear_Starter_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'codegear-starter',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

$plugin_i18n = new Codegear_Starter_i18n();
$plugin_i18n->load_plugin_textdomain();
