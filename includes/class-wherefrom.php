<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.wherefrom.org
 *
 * @package    Wherefrom
 * @subpackage Wherefrom/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    Wherefrom
 * @subpackage Wherefrom/includes
 * @author     Wherefrom LTD <tech@wherefrom.org>
 */
class Wherefrom {
	/**
	 * The current version of the plugin.
	 *
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @access   protected
	 * @var      Wherefrom_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * shortcodes handler instance
	 */
	protected $shortcodes;

	/**
	 * woocommerce handler instance
	 */
	protected $wc;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 */
	public function __construct() {
		if ( defined( 'WHEREFROM_VERSION' ) ) {
			$this->version = WHEREFROM_VERSION;
		} else {
			$this->version = '1.2.8';
		}

		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wherefrom_Loader. Orchestrates the hooks of the plugin.
	 * - Wherefrom_i18n. Defines internationalization functionality.
	 * - Wherefrom_Admin. Defines all hooks for the admin area.
	 * - Wherefrom_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wherefrom-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wherefrom-shortcodes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wherefrom-wc.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wherefrom-utils.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wherefrom-admin.php';

		$this->loader = new Wherefrom_Loader();
		$this->shortcodes = new Wherefrom_Shortcodes();

		if (WherefromUtils::isWooCommerceActive()) {
			$this->wc = new Wherefrom_WC();
		}
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Wherefrom_Admin( $this->version );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin, 'init_api_routes' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
		$this->shortcodes->register();

		if (WherefromUtils::isWooCommerceActive() && $this->wc) {
			$this->wc->register();
		}
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Wherefrom_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
}
