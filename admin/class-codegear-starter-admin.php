<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://codegearthemes.com
 * @since      1.0.0
 *
 * @package    Codegear_Starter
 * @subpackage Codegear_Starter/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Codegear_Starter
 * @subpackage Codegear_Starter/admin
 * @author     CodeGearThemes <info@codegearthemes.com>
 */
class Codegear_Starter_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	/**
	 * The slug name to refer to this menu by.
	 *
	 * @var string $menu_slug The menu slug.
	 */
	public $menu_slug = 'starter';

	/**
	 * The pro_status of theme.
	 *
	 * @var string $pro_status The pro_status.
	 */
	public $pro_status = false;

	/**
	 * The settings of page.
	 *
	 * @var array $settings The settings.
	 */
	public $settings = array(
		'pro_label'  => 'Get Pro',
		'pro_link'   => '#',
		'categories' => array(),
		'builders'   => array(),
	);

	/**
	 * The demos of page.
	 *
	 * @var array $demos The demos.
	 */
	public $demos = array();

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		if ( in_array( get_option( 'template' ), $this->get_core_supported_themes(), true ) ) {
		
			add_action( 'init', array( $this, 'set_demos' ) );
			add_action( 'init', array( $this, 'set_settings' ) );

			add_action( 'wp_ajax_codegear_import_theme', array( $this, 'codegear_import_theme' ) );
			add_action( 'wp_ajax_nopriv_codegear_import_theme', array( $this, 'codegear_import_theme' ) );

			add_action( 'init', function ()  {
				add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
			} );

			add_action( 'plugins_loaded', array( $this, 'theme_configs_files' ) );
		}else{
			add_action( 'admin_notices', array( $this, 'theme_support_missing_notice' ) );
		}

	}

	/**
	 * Add menu page
	 */
	public function add_menu_page() {
		add_submenu_page( 'themes.php', esc_html__( 'Starter Sites', 'codegear-starter' ), esc_html__( 'Starter Sites', 'codegear-starter' ), 'manage_options', $this->menu_slug, array( $this, 'html_carcase' ), 2 );
	}


	/**
	 * Load theme config files
	 */
	public function theme_configs_files() {

		$theme  = wp_get_theme();
		$parent = wp_get_theme()->parent();

		if ( get_template_directory() !== get_stylesheet_directory() ) {
			$parent = wp_get_theme()->parent();
			$parent = $parent->name;
		}

		if ( 'Cosme' === $theme->name || 'Cosme' === $parent ) {
			require_once CODEGEAR_STARTER_PATH . 'templates/cosme.php';
		}
	}


	/**
	 * Settings
	 *
	 * @param array $settings The settings.
	 */
	public function set_settings( $settings ) {
		$this->settings = apply_filters( 'codegear_register_demos_settings', $this->settings );

		if ( isset( $this->settings['pro_status'] ) ) {
			$this->pro_status = $this->settings['pro_status'];
		}

	}

	/**
	 * Demos
	 *
	 * @param array $demos The demos.
	 */
	public function set_demos() {
		$this->demos = apply_filters( 'codegear_register_demos_list', $this->demos );
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Codegear_Starter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Codegear_Starter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, CODEGEAR_STARTER_URL. 'assets/css/admin.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Codegear_Starter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Codegear_Starter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, CODEGEAR_STARTER_URL.'assets/js/admin.min.js', array(), $this->version, false );

		wp_localize_script( $this->plugin_name, 'Codegear_Starter_localize', array(
			'ajax_url'       => admin_url( 'admin-ajax.php' ),
			'nonce'          => wp_create_nonce( 'nonce' ),
			'failed_message' => esc_html__( 'Something went wrong, contact support.', 'codegear-starter' ),
		) );

	}

	/**
	 * Get core supported themes.
	 *
	 * @return array
	 */
	private function get_core_supported_themes() {
		$themes = array(
			'acoustics',
			'cosme'
		);
	
		return $themes;
	}

	/**
	 * Theme support fallback notice.
	 */
	public function theme_support_missing_notice() {
		$themes_url = array_intersect( array_keys( wp_get_themes() ), $this->get_core_supported_themes() ) ? admin_url( 'themes.php?search=codegearthemes' ) : admin_url( 'theme-install.php?search=cosme' ); ?>
		<div class="error notice is-dismissible">
			<p>
				<strong> 
					<?php echo esc_html__( 'Codegear Starter', 'codegear-starter' ); ?>
					&#58;
				</strong>
				<?php echo sprintf( esc_html__( 'This plugin requires %s themes to be installed and activated.', 'codegear-starter' ), '<a href="' . esc_url( $themes_url ) . '">' . esc_html__( 'Official CodeGearThemes', 'codegear-starter' ) . '</a>' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Import theme data
	 */
	public function codegear_import_theme() {
		check_ajax_referer( 'nonce', 'nonce' );

		$demo_id = isset( $_POST['demo_id'] ) ? sanitize_key( wp_unslash( $_POST['demo_id'] )) : false;

		if ( $demo_id ) {

			if ( ! isset( $this->demos[ $demo_id ] ) ) {
				wp_send_json_error( esc_html__( 'Invalid demo content.', 'codegear-starter' ) );
				wp_die();
			}

			// Reset import data.
			delete_transient( 'codegear_starter_importer_data' );

			$demo_data = $this->demos[ $demo_id ];

			ob_start();
			?>
			<div class="starter-import-data">
				<?php if ( isset( $demo_data['plugins'] ) && $demo_data['plugins'] ) { ?>
					<div class="starter-import-plugins">
						<div class="starter-import-subheader">
							<?php esc_html_e( 'Install Plugins', 'codegear-starter' ); ?>
						</div>

						<?php
						foreach ( $demo_data['plugins'] as $plugin ) {
							if ( isset( $plugin['name'] ) ) {
								$name = $plugin['name'];
							} else {
								continue;
							}
							if ( isset( $plugin['slug'] ) ) {
								$slug = $plugin['slug'];
							} else {
								continue;
							}
							if ( isset( $plugin['path'] ) ) {
								$path = $plugin['path'];
							} else {
								continue;
							}

							$required = isset( $plugin['required'] ) ? $plugin['required'] : false;
							?>
							<form class="block--form-step active">
								<div class="starter-switcher">
									<?php echo esc_html( $plugin['name'] ); ?> <input class="starter-checkbox" type="checkbox" name="<?php echo esc_attr( $plugin['slug'] ); ?>" value="1" <?php echo wp_kses_post( $required ? 'readony onclick="return false;"' : null ); ?> checked>
									<div class="starter-switch"><span class="starter-switch-slider"></span></div>
									<div class="starter-tooltip"><?php esc_html_e( 'Required plugin will be installed', 'codegear-starter' ); ?></div>
								</div>
								<input type="hidden" name="demo_id" value="<?php echo esc_attr( $demo_id ); ?>">
								<input type="hidden" name="plugin_slug" value="<?php echo esc_attr( $plugin['slug'] ); ?>">
								<input type="hidden" name="plugin_path" value="<?php echo esc_attr( $plugin['path'] ); ?>">
								<input type="hidden" name="step_name" value="<?php esc_html_e( 'Installing and activating', 'codegear-starter' ); ?> <?php echo esc_attr( $plugin['name'] ); ?>...">
								<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'nonce' ) ); ?>">
								<input type="hidden" name="action" value="codegear_starter_import_plugin">
							</form>
						<?php } ?>
					</div>
				<?php } ?>

				<div class="starter-import-content">
					<div class="starter-import-subheader">
						<?php esc_html_e( 'Import Content', 'codegear-starter' ); ?>
					</div>

					<?php if ( isset( $demo_data['import']['content'] ) && $demo_data['import']['content'] ) { ?>
						<form class="block--form-step">
							<div class="starter-switcher">
								<?php esc_html_e( 'Content', 'codegear-starter' ); ?> <input class="starter-checkbox" type="checkbox" name="url" value="<?php echo esc_url( $demo_data['import']['content'] ); ?>" checked>
								<div class="starter-switch"><span class="starter-switch-slider"></span></div>
								<input type="hidden" name="demo_id" value="<?php echo esc_attr( $demo_id ); ?>">
								<input type="hidden" name="step_name" value="<?php esc_html_e( 'Importing contents...', 'codegear-starter' ); ?>">
								<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'nonce' ) ); ?>">
								<input type="hidden" name="action" value="codegear_starter_import_contents">
							</div>
						</form>
					<?php } ?>

					<?php if ( isset( $demo_data['import']['widgets'] ) && $demo_data['import']['widgets'] ) { ?>
						<form class="block--form-step">
							<div class="starter-switcher">
								<?php esc_html_e( 'Widgets', 'codegear-starter' ); ?> <input class="starter-checkbox" type="checkbox" name="url" value="<?php echo esc_url( $demo_data['import']['widgets'] ); ?>" checked>
								<div class="starter-switch"><span class="starter-switch-slider"></span></div>
							</div>
							<input type="hidden" name="demo_id" value="<?php echo esc_attr( $demo_id ); ?>">
							<input type="hidden" name="step_name" value="<?php esc_html_e( 'Importing widgets...', 'codegear-starter' ); ?>">
							<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'nonce' ) ); ?>">
							<input type="hidden" name="action" value="codegear_starter_import_widgets">
						</form>
					<?php } ?>

					<?php if ( isset( $demo_data['import']['customizer'] ) && $demo_data['import']['customizer'] ) { ?>
						<form class="block--form-step">
							<div class="starter-switcher">
								<?php esc_html_e( 'Customizer', 'codegear-starter' ); ?> <input class="starter-checkbox" type="checkbox" name="url" value="<?php echo esc_url( $demo_data['import']['customizer'] ); ?>" checked>
								<div class="starter-switch"><span class="starter-switch-slider"></span></div>
							</div>
							<input type="hidden" name="demo_id" value="<?php echo esc_attr( $demo_id ); ?>">
							<input type="hidden" name="step_name" value="<?php esc_html_e( 'Importing customizer options...', 'codegear-starter' ); ?>">
							<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'nonce' ) ); ?>">
							<input type="hidden" name="action" value="codegear_starter_import_customizer">
						</form>
					<?php } ?>

					<?php if ( isset( $demo_data['import']['options'] ) && $demo_data['import']['options'] ) { ?>
						<form class="block--form-step">
							<div class="starter-switcher">
								<?php esc_html_e( 'Options', 'codegear-starter' ); ?> <input class="starter-checkbox" type="checkbox" name="url" value="<?php echo esc_url( $demo_data['import']['options'] ); ?>" checked>
								<div class="starter-switch"><span class="starter-switch-slider"></span></div>
							</div>
							<input type="hidden" name="step_name" value="<?php esc_html_e( 'Importing options...', 'codegear-starter' ); ?>">
							<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'nonce' ) ); ?>">
							<input type="hidden" name="action" value="codegear_starter_import_options">
						</form>
					<?php } ?>

					<form class="block--form-step hidden">
						<div class="starter-switcher">
							<?php esc_html_e( 'Finish', 'codegear-starter' ); ?> <input class="starter-checkbox" type="checkbox" name="finish" value="1" checked>
							<div class="starter-switch"><span class="starter-switch-slider"></span></div>
						</div>
						<input type="hidden" name="step_name" value="<?php esc_html_e( 'Finishing setup...', 'codegear-starter' ); ?>">
						<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'nonce' ) ); ?>">
						<input type="hidden" name="action" value="codegear_starter_import_finish">
					</form>
				</div>
			</div>

			<?php
			wp_send_json_success( ob_get_clean() );
		} else {
			wp_send_json_error( esc_html__( 'Invalid demo content.', 'codegear-starter' ) );
		}

		wp_die();
	}

	/**
	 * Html Carcase
	 */
	public function html_carcase() { ?>
		<div class="section--demo-page">
			<div class="header">
				<div class="column-half header-left">
					<div class="branding">
						<svg width="250" xmlns="http://www.w3.org/2000/svg" id="CodeGearThemes" data-name="CodeGearThemes" viewBox="0 0 417.58 115.33">
							<path class="path" fill="#112c91" d="M149.43,73q-1.2-6.66-7.86-6.73a12.78,12.78,0,0,0-3,.4,7.77,7.77,0,0,0-3.05,2,10.42,10.42,0,0,0-2.34,3.93,18.74,18.74,0,0,0-1,6.72,16.24,16.24,0,0,0,2.14,8.32,7.45,7.45,0,0,0,7,3.92q6.84-.14,8.18-8.15H157q-2.28,14.25-15.74,14.39-8.12-.14-12.31-5.34-4.38-5.14-4.38-13.15,0-8.33,4.32-13.73T141.52,60A17.48,17.48,0,0,1,152,63.1q4.35,3.2,5,9.86Z" transform="translate(-4.77 -7.17)"/>
							<path class="path" fill="#112c91" d="M180.17,97.72q-8.32-.07-12.78-5.41-4.57-5.15-4.58-13.49t4.58-13.59q4.44-5.18,12.76-5.24T193,65.23q4.49,5.2,4.48,13.59T193,92.31Q188.54,97.65,180.17,97.72Zm0-6.24a8.23,8.23,0,0,0,7.47-4.06,17.24,17.24,0,0,0,0-17.24,8.49,8.49,0,0,0-7.47-4,8.32,8.32,0,0,0-7.39,4,16.68,16.68,0,0,0,0,17.24A8.07,8.07,0,0,0,180.17,91.48Z" transform="translate(-4.77 -7.17)"/>
							<path class="path" fill="#112c91" d="M237.47,96.74h-7.18V91.88h-.13a9.51,9.51,0,0,1-4.39,4.38,13.59,13.59,0,0,1-6.27,1.46q-8.06-.14-12-5.61t-4.06-13.53q.12-9.66,4.64-14.16A14.08,14.08,0,0,1,218.3,60a16.9,16.9,0,0,1,6.53,1.29,10.47,10.47,0,0,1,4.94,4.3h.13V47.13h7.57ZM211,79.24a15.13,15.13,0,0,0,2.34,8.39,8,8,0,0,0,7.18,3.85A8.16,8.16,0,0,0,228,87.42a16.61,16.61,0,0,0,2.21-8.58q-.19-12.41-9.52-12.61-5.19.13-7.44,4A17.24,17.24,0,0,0,211,79.24Z" transform="translate(-4.77 -7.17)"/>
							<path class="path" fill="#112c91" d="M252.92,80.82a12,12,0,0,0,2.27,7.39c1.53,2.13,3.86,3.23,7,3.27q6.48,0,8.37-6h7.17a15.11,15.11,0,0,1-5.52,9,16.73,16.73,0,0,1-10,3.22q-8.12-.14-12.43-5.34t-4.38-13.49a20.06,20.06,0,0,1,4.51-13.32Q254.24,60.13,262,60q8.31.13,12.69,6.34a20.13,20.13,0,0,1,3.51,14.49ZM270.6,75.6A10.85,10.85,0,0,0,268.13,69,7.75,7.75,0,0,0,262,66.23a8.57,8.57,0,0,0-6.38,2.68,10.52,10.52,0,0,0-2.66,6.69Z" transform="translate(-4.77 -7.17)"/>
							<path class="path" fill="#ef0d33" d="M317,94.87Q316.77,111,300.08,111a20.37,20.37,0,0,1-10-2.51q-4.65-2.52-5.36-8.69h7.58a6,6,0,0,0,3.05,4.31,12.93,12.93,0,0,0,5.2,1q8.91-.07,8.85-9.2V90.43h-.14q-3.51,6.24-10.3,6.31-8.13-.08-11.8-5.62a22.3,22.3,0,0,1-3.64-13.21,19.31,19.31,0,0,1,4.19-12.61q4.07-5.17,11.38-5.31a14.38,14.38,0,0,1,6,1.25,9.76,9.76,0,0,1,4.13,4.52h.13v-5H317Zm-16.8-4.37q5-.07,7.18-4A17.42,17.42,0,0,0,309.39,78a15.66,15.66,0,0,0-2.11-8.3c-1.43-2.3-3.81-3.47-7.12-3.51s-5.63,1.41-7,4a16.37,16.37,0,0,0-2.08,8.5,14.85,14.85,0,0,0,2.21,8.07A7.55,7.55,0,0,0,300.16,90.5Z" transform="translate(-4.77 -7.17)"/>
							<path class="path" fill="#ef0d33" d="M331.77,80.82A12.09,12.09,0,0,0,334,88.21a8.2,8.2,0,0,0,7,3.27q6.5,0,8.37-6h7.18a15.11,15.11,0,0,1-5.52,9,16.78,16.78,0,0,1-10,3.22q-8.11-.14-12.43-5.34T324.2,78.89a20.07,20.07,0,0,1,4.52-13.32q4.38-5.44,12.1-5.58,8.31.13,12.69,6.34T357,80.82Zm17.69-5.22A10.91,10.91,0,0,0,347,69a7.78,7.78,0,0,0-6.17-2.81,8.58,8.58,0,0,0-6.39,2.68,10.52,10.52,0,0,0-2.66,6.69Z" transform="translate(-4.77 -7.17)"/>
							<path class="path" fill="#ef0d33" d="M392.83,88.76c0,1.86.56,2.77,1.8,2.72a18.3,18.3,0,0,0,1.86-.14v5.47a14.76,14.76,0,0,1-4.78.91,7,7,0,0,1-3.81-1,5.33,5.33,0,0,1-2-3.4,14.09,14.09,0,0,1-5.46,3.26,19.91,19.91,0,0,1-6.48,1.12,13.2,13.2,0,0,1-8.2-2.64q-3.51-2.64-3.58-7.86a11.3,11.3,0,0,1,1.18-5.52,8.06,8.06,0,0,1,3.09-3.22,18.79,18.79,0,0,1,4.27-1.73,40.32,40.32,0,0,1,4.72-.67c1.43-.3,2.77-.53,4-.71a24.57,24.57,0,0,0,3.26-.54,3.57,3.57,0,0,0,3.06-3.8q-.08-3-2.38-4a12,12,0,0,0-4.75-.81,9.06,9.06,0,0,0-5.17,1.31A5.79,5.79,0,0,0,371,71.88h-7.56A13.5,13.5,0,0,1,365.13,66a10.55,10.55,0,0,1,3.57-3.61A20.68,20.68,0,0,1,379.08,60a19.1,19.1,0,0,1,9.41,2.26q4.17,2.45,4.34,8.12Zm-7.57-10a11.42,11.42,0,0,1-4.2,1.39q-2.55.35-5.16.75a9.82,9.82,0,0,0-4.27,1.6,5,5,0,0,0-1.92,4.39,3.69,3.69,0,0,0,2.54,3.58,9.4,9.4,0,0,0,4.47,1,10.48,10.48,0,0,0,2.8-.41,12,12,0,0,0,2.74-1.19,7.24,7.24,0,0,0,2.22-2.08,5.84,5.84,0,0,0,.78-3.13Z" transform="translate(-4.77 -7.17)"/>
							<path class="path" fill="#ef0d33" d="M402.22,60.8h7.11v6.92h.13a10.68,10.68,0,0,1,3.67-5.37A10,10,0,0,1,419.5,60a19.43,19.43,0,0,1,2.85.2v7.6l-1.59-.27c-.52,0-1.07-.07-1.65-.07a8.21,8.21,0,0,0-6.59,3.11q-2.67,3.18-2.73,9.13v17h-7.57Z" transform="translate(-4.77 -7.17)"/>
							<path d="M337.09,121V105h-6v-2.14h14.41V105h-6v16Z" transform="translate(-4.77 -7.17)"/>
							<path d="M347.69,121V102.86h2.24v6.52a5,5,0,0,1,3.94-1.81,5.24,5.24,0,0,1,2.54.58,3.36,3.36,0,0,1,1.55,1.59,7.41,7.41,0,0,1,.46,3V121h-2.23V112.7a3.42,3.42,0,0,0-.73-2.44,2.67,2.67,0,0,0-2-.76,3.68,3.68,0,0,0-1.87.51,2.9,2.9,0,0,0-1.24,1.4,6.32,6.32,0,0,0-.37,2.43V121Z" transform="translate(-4.77 -7.17)"/>
							<path d="M370.84,116.8l2.31.29a5.68,5.68,0,0,1-2,3.14,6.11,6.11,0,0,1-3.77,1.11,6,6,0,0,1-4.59-1.78,6.93,6.93,0,0,1-1.69-5,7.27,7.27,0,0,1,1.71-5.16,5.77,5.77,0,0,1,4.44-1.84,5.64,5.64,0,0,1,4.32,1.8,7.14,7.14,0,0,1,1.67,5.06c0,.13,0,.33,0,.6h-9.82a5.08,5.08,0,0,0,1.22,3.32,3.68,3.68,0,0,0,2.76,1.16,3.38,3.38,0,0,0,2.09-.65A4.3,4.3,0,0,0,370.84,116.8Zm-7.33-3.61h7.36a4.39,4.39,0,0,0-.85-2.49,3.41,3.41,0,0,0-2.76-1.29,3.54,3.54,0,0,0-2.59,1A4,4,0,0,0,363.51,113.19Z" transform="translate(-4.77 -7.17)"/>
							<path d="M376,121V107.87h2v1.85a4.58,4.58,0,0,1,1.65-1.56,4.67,4.67,0,0,1,2.34-.59,4.27,4.27,0,0,1,2.4.61,3.23,3.23,0,0,1,1.33,1.7,4.71,4.71,0,0,1,4.06-2.31,4,4,0,0,1,3,1.09A4.65,4.65,0,0,1,393.8,112v9h-2.22v-8.29a6.27,6.27,0,0,0-.21-1.93,1.87,1.87,0,0,0-.79-.95,2.46,2.46,0,0,0-1.34-.36,3.14,3.14,0,0,0-2.31.92,4.08,4.08,0,0,0-.92,3V121h-2.23v-8.56a3.72,3.72,0,0,0-.55-2.23,2,2,0,0,0-1.78-.74,3.28,3.28,0,0,0-1.75.49,2.84,2.84,0,0,0-1.16,1.46,8.11,8.11,0,0,0-.36,2.75V121Z" transform="translate(-4.77 -7.17)"/>
							<path d="M406.13,116.8l2.31.29a5.68,5.68,0,0,1-2,3.14,6.11,6.11,0,0,1-3.77,1.11,6,6,0,0,1-4.59-1.78,7,7,0,0,1-1.69-5,7.27,7.27,0,0,1,1.71-5.16,5.77,5.77,0,0,1,4.44-1.84,5.63,5.63,0,0,1,4.32,1.8,7.14,7.14,0,0,1,1.68,5.06c0,.13,0,.33,0,.6h-9.82a5,5,0,0,0,1.23,3.32,3.64,3.64,0,0,0,2.75,1.16,3.43,3.43,0,0,0,2.1-.65A4.36,4.36,0,0,0,406.13,116.8Zm-7.33-3.61h7.36a4.39,4.39,0,0,0-.85-2.49,3.41,3.41,0,0,0-2.76-1.29,3.54,3.54,0,0,0-2.59,1A4.07,4.07,0,0,0,398.8,113.19Z" transform="translate(-4.77 -7.17)"/>
							<path d="M410.35,117.11l2.21-.35a3.09,3.09,0,0,0,1,2,3.63,3.63,0,0,0,2.38.71,3.45,3.45,0,0,0,2.28-.63,1.88,1.88,0,0,0,.75-1.47,1.36,1.36,0,0,0-.66-1.19,9.9,9.9,0,0,0-2.28-.76,20.66,20.66,0,0,1-3.41-1.07,3.34,3.34,0,0,1-1.44-1.25,3.42,3.42,0,0,1-.49-1.77,3.37,3.37,0,0,1,.41-1.63,3.54,3.54,0,0,1,1.09-1.25,4.59,4.59,0,0,1,1.42-.65,6.72,6.72,0,0,1,1.93-.27,7.62,7.62,0,0,1,2.73.45,3.76,3.76,0,0,1,1.73,1.21,4.76,4.76,0,0,1,.77,2l-2.19.3a2.37,2.37,0,0,0-.86-1.59,3.17,3.17,0,0,0-2-.57,3.64,3.64,0,0,0-2.19.51,1.47,1.47,0,0,0-.66,1.19,1.23,1.23,0,0,0,.27.78,2,2,0,0,0,.86.59c.22.09.88.28,2,.57a29.1,29.1,0,0,1,3.31,1,3.43,3.43,0,0,1,1.47,1.17,3.33,3.33,0,0,1,.53,1.91,3.72,3.72,0,0,1-.65,2.11,4.22,4.22,0,0,1-1.88,1.52,6.83,6.83,0,0,1-2.78.54,6.13,6.13,0,0,1-3.91-1.07A4.94,4.94,0,0,1,410.35,117.11Z" transform="translate(-4.77 -7.17)"/>
							<path class="path" fill="#112c91" d="M118,55.36l-14.81-2.42a42.6,42.6,0,0,0-3.54-8.54l8.65-12.33a2.5,2.5,0,0,0-.28-3.21l-9.75-9.75A2.51,2.51,0,0,0,95,18.85L82.88,27.58A42.09,42.09,0,0,0,74.27,24L71.69,9.24a2.5,2.5,0,0,0-2.47-2.07H55.43A2.51,2.51,0,0,0,53,9.27L50.56,24a41.9,41.9,0,0,0-8.62,3.54L29.83,18.84a2.51,2.51,0,0,0-3.23.26l-9.75,9.75a2.5,2.5,0,0,0-.28,3.21l8.52,12.19a42.84,42.84,0,0,0-3.6,8.68L6.87,55.36a2.51,2.51,0,0,0-2.1,2.47V71.62a2.5,2.5,0,0,0,2.07,2.47l14.62,2.6a42.13,42.13,0,0,0,3.61,8.67L16.44,97.43a2.52,2.52,0,0,0,.26,3.23l9.75,9.76a2.5,2.5,0,0,0,3.21.28l12.21-8.56a41.75,41.75,0,0,0,8.64,3.57L53,120.41a2.49,2.49,0,0,0,2.47,2.09H69.22a2.52,2.52,0,0,0,2.47-2.07l2.62-14.77a42.13,42.13,0,0,0,8.6-3.59l12.29,8.62a2.5,2.5,0,0,0,3.22-.27l9.75-9.76a2.52,2.52,0,0,0,.26-3.24L99.66,85.24a42.43,42.43,0,0,0,3.52-8.54L118,74.09a2.51,2.51,0,0,0,2.08-2.47V57.83A2.5,2.5,0,0,0,118,55.36Zm-55.56,27A17.55,17.55,0,1,1,80,64.83,17.55,17.55,0,0,1,62.43,82.38Z" transform="translate(-4.77 -7.17)"/>
						</svg>
					</div>
				</div>
				<div class="column-half header-right">

				</div>
			</div>
			<div class="wrap main">
				<h2 class="hidden hide"><?php esc_html_e( 'Starter Sites', 'codegear-starter' ); ?></h2>
				<div class="section--demo-wrapper">
					<?php if ( $this->demos ):
						foreach ( $this->demos as $demo_id => $demo ):
							$name    = isset( $demo['name'] ) && $demo['name'] ? $demo['name'] : null;
							$type    = isset( $demo['type'] ) && $demo['type'] ? $demo['type'] : null;
							$builder = isset( $demo['builder'] ) && $demo['builder'] ? $demo['builder'] : null;
							$preview = isset( $demo['preview'] ) && $demo['preview'] ? $demo['preview'] : 'false';

							$categories = '[]';

							if ( isset( $demo['categories'] ) && $demo['categories'] ) {
								foreach ( $demo['categories'] as $category ) {
									$categories .= sprintf( '[%s]', $category );
								}
							}

							// Builders.
							$builders = '[]';

							if ( isset( $demo['builders'] ) && $demo['builders'] ) {
								foreach ( $demo['builders'] as $builder ) {
									$builders .= sprintf( '[%s]', $builder );
								}
							} ?>
							<div class="grid-item demo--item-grid"
								data-id="<?php echo esc_attr( $demo_id ); ?>"
								data-name="<?php echo esc_attr( $name ); ?>"
								data-type="<?php echo esc_attr( $type ); ?>"
								data-categories="<?php echo esc_attr( $categories ); ?>"
								data-builders="<?php echo esc_attr( $builders ); ?>"
								data-preview="<?php echo esc_url( $preview ); ?>">

								<div class="item-outer">
									<div class="item--content-inner">
										<div class="screen-image">
											<?php if ( isset( $demo['thumbnail'] ) && $demo['thumbnail'] ) { ?>
												<img width="410" height="275" src="<?php echo esc_url( $demo['thumbnail'] ); ?>" alt="<?php echo esc_html( $demo['name'] ); ?>">
											<?php } ?>

											<?php if ( isset( $demo['preview'] ) && $demo['preview'] ) { ?>
												<a href="<?php echo esc_url( $demo['preview'] ) ?>" class="preview" target="_blank">
													<span>
														<?php esc_html_e( 'Preview Demo', 'codegear-starter' ); ?>
													</span>
												</a>
											<?php } ?>
										</div>
										<div class="template-data">
											<div class="product-info">
												<?php if ( isset( $demo['name'] ) && $demo['name'] ) { ?>
													<div class="name"><?php echo esc_html( $demo['name'] ); ?></div>
												<?php } ?>
												<?php if ( ! $this->pro_status && isset( $demo['type'] ) && $demo['type'] ) { ?>
													<?php if ( 'free' === $demo['type'] ) { ?>
														<div class="badge badge-success"><?php echo esc_html( $demo['type'] ); ?></div>
													<?php } ?>

													<?php if ( 'pro' === $demo['type'] ) { ?>
														<div class="badge badge-info"><?php echo esc_html( $demo['type'] ); ?></div>
													<?php } ?>
												<?php } ?>
											</div>

											<?php if ( isset( $demo['type'] ) && $demo['type'] ) { ?>
												<div class="actions-btn">
													<?php if ( $this->pro_status || 'free' === $demo['type'] ) { ?>
														<div class="demo-free-version">
															<a href="#" target="_blank" data-id="<?php echo esc_attr( $demo_id ); ?>" class="button-import button button-primary">
																<?php esc_html_e( 'Import', 'codegear-starter' ); ?>
															</a>
														</div>
													<?php } ?>

													<?php if ( ! $this->pro_status && 'pro' === $demo['type'] ) { ?>
														<div class="demo-premium-version">
															<a href="<?php echo esc_url( $this->settings['pro_link'] ); ?>" target="_blank" class="button--import-url button button-primary">
																<?php esc_html_e( 'Get Pro', 'codegear-starter' ); ?>
															</a>
														</div>
													<?php } ?>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach;
					endif; ?>
				</div>
			</div>

			<div class="block--import-theme">
				<div class="block--import-overlay"></div>

				<div class="block--import-popup">
					<div class="container">
						<div class="block-import-step block-import-active block-import-start">
							<div class="import-header">
								<?php esc_html_e( 'Import Theme', 'codegear-starter' ); ?>
							</div>

							<div id="block-import-output" class="block--import-output"></div>
							<div class="starter-import-actions">
								<div class="starter-import-theme-cancel">
									<a id="starter-demo-import-close" href="#" class="starter-button starter-demo-import-close button">
										<?php esc_html_e( 'Cancel', 'codegear-starter' ); ?>
									</a>
								</div>

								<div class="starter-import-theme-start">
									<a href="#" class="starter-demo-import-start button button-primary">
										<?php esc_html_e( 'Import', 'codegear-starter' ); ?>
									</a>
								</div>
							</div>
						</div>

						<div class="block-import-step block-import-process">
							<div class="block-import-header">
								<?php esc_html_e( 'Installing', 'codegear-starter' ); ?>
							</div>

							<div class="block-import-output">
								<div class="block-import-desc">
									<?php esc_html_e( 'Please be patient and don\'t refresh this page, the import process may take a while, this also depends on your server.', 'codegear-starter' ); ?>
								</div>
								<div id="message_output" class="message-output"></div>
								<div class="block-import-progress">
									<div class="block-import-label"></div>

									<div class="block-import-progress-bar">
										<div class="block--import-progress-indicator" style="--app-progress: 0%;"></div>
									</div>

									<div class="block--import-progress-label">0%</div>
								</div>
							</div>
						</div>

						<div class="block-import-step block--import-finish">
							<div class="block--import-info">
								<div class="block--import-logo">
									<svg class="progress-icon checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"> 
										<circle class="checkmark--circle" cx="26" cy="26" r="25" fill="none"/> 
										<path class="checkmark--check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
									</svg>
								</div>

								<div class="block--import-title">
									<?php esc_html_e( 'Imported Successfully', 'codegear-starter' ); ?>
								</div>

								<div class="block-import-desc">
									<?php esc_html_e( 'Go ahead, customize the text, images and design to make it yours!', 'codegear-starter' ); ?>
								</div>

								<div class="block-import-customize">
									<a href="<?php echo esc_url( admin_url( '/customize.php' ) ); ?>" class="button button-primary" target="_blank">
										<?php esc_html_e( 'Customize', 'codegear-starter' ); ?>
									</a>
								</div>
							</div>

							<div class="block--import-actions">
								<a href="<?php echo esc_url( admin_url() ); ?>" class="text-link">
									<?php esc_html_e( 'Return to Dashboard', 'codegear-starter' ); ?>
								</a>

								<a href="<?php echo esc_url( home_url() ); ?>" class="block-button button" target="_blank">
									<?php esc_html_e( 'View Site', 'codegear-starter' ); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

}
