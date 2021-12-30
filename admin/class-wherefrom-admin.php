<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.wherefrom.org
 *
 * @package    Wherefrom
 * @subpackage Wherefrom/admin
 */

require_once 'utils/general.php';
require_once 'utils/ui.php';
require_once 'utils/wc.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wherefrom
 * @subpackage Wherefrom/admin
 * @author     Wherefrom LTD <tech@wherefrom.org>
 */
class Wherefrom_Admin {

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialisez the pliugin
	 */
	public function __construct( $version ) {
		$this->version = $version;
		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 9);   
		add_action('admin_init', array( $this, 'registerAndBuildFields' )); 
	}

	/**
	 * registers stylesheets
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'jquery.multi-select', plugin_dir_url( __FILE__ ) . 'css/jquery.multi-select.dist.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jquery.steps', plugin_dir_url( __FILE__ ) . 'css/jquery.steps.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'lds', plugin_dir_url( __FILE__ ) . 'css/lds.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'wherefrom-admin', plugin_dir_url( __FILE__ ) . 'css/wherefrom-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'fa', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/fontawesome.min.css', array(), $this->version, 'all' );
	}

	/**
	 * registers custom scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery.steps', plugin_dir_url( __FILE__ ) . 'js/jquery.steps.min.js', array(), $this->version, true );
		wp_enqueue_script( 'jquery.multi-select', plugin_dir_url( __FILE__ ) . 'js/jquery.multi-select.js', array(), $this->version, true );
		wp_enqueue_script( 'wherefrom-wc-admin', plugin_dir_url( __FILE__ ) . 'js/wherefrom-wc-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'wherefrom-setup', plugin_dir_url( __FILE__ ) . 'js/wherefrom-setup.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( 'wherefrom-setup', 'wf', array(
			'restURL' => rest_url(),
			'restNonce' => wp_create_nonce('wp_rest')
		));
	}

	/**
	 * initialises api routes for admin
	 */
	public function init_api_routes() {
		register_rest_route('wf/v1/settings', '/seo-name/', array(
			'methods' => 'POST',
			'callback' => array( $this, 'handleSeoName')
		));
		register_rest_route('wf/v1/settings', '/api-key/', array(
			'methods' => 'POST',
			'callback' => array( $this, 'handleApiKey')
		));
		register_rest_route('wf/v1/products', '/csv/', array(
			'methods' => 'GET',
			'callback' => array( $this, 'handlePeroductsCsv')
		));
		register_rest_route('wf/v1/products', '/sync/', array(
			'methods' => 'GET',
			'callback' => array( $this, 'handleProductSync')
		));
	}

	public function handleSeoName() {
		$value = sanitize_text_field($_POST['seoName']);

		update_option('wherefrom_seo_name', trim($value));
		$response = array('seoName' => $value);

		echo json_encode($response);
		die;
	}

	public function handleApiKey() {
		$value = sanitize_text_field($_POST['key']);

		update_option('wherefrom_api_key', trim($value));
		$response = array('key' => $value);

		echo json_encode($response);
		die;
	}

	public function handleProductSync() {
		$filter = array(
			'status' => 'publish',
			'limit' => -1
		);

		$results = wc_get_products($filter);

		$products = array();

		// Loop through products and display some data using WC_Product methods
		foreach ( $results as $product ){
			$products[] = WHEREFROM_buildProduct($product);
		}

		$response = WHEREFROM_postProducts($products);
		die();
	}

	public function handlePeroductsCsv() {
		$idField = get_option('wherefrom_id_field', 'SKU' );
		$categoriesToExclude = get_option('wherefrom_categories_to_exclude', array());

		if ( ! wc_product_sku_enabled() && $idField === 'SKU' ) {
			$idField = "ID";
		}

		$filter = array(
			'status' => 'publish',
			'limit' => -1
		);

		if (isset($_GET['afterLastExport'])) {
			$lastExportTimeStamp = get_option('wherefrom_last_export_timestamp', null );

			if ($lastExportTimeStamp) {
				$filter['date_created'] = '>='.$lastExportTimeStamp;
			}
		}
		$results = wc_get_products($filter);

		$products = array();

		// Loop through products and display some data using WC_Product methods
		foreach ( $results as $product ){
			$id = $idField === 'SKU' ? $product->get_sku() : $product->get_id();

			$terms = get_the_terms( $product->get_id(), 'product_brand' );
			$brand_name = null;
			foreach ( $terms as $term ){
				if ( $term->parent == 0 ) {
					$brand_name = $term->slug;
				}
			}  

			$categoryTerms = get_the_terms( $product->get_id(), 'product_cat' );
			$categories = [];

			$categories = wherefrom_getAllCategoriesForProduct($product->get_id());

			$categoryL1 = array();
			$categoryL2 = array();
			$categoryL3 = array();

			foreach($categories as $category) {
				$categoryChunks = explode(" >> ", $category);

				if ($categoryChunks[0]) {
					$categoryL1[] = $categoryChunks[0];
				}
				if ($categoryChunks[1]) {
					$categoryL2[] = $categoryChunks[1];
				}
				if ($categoryChunks[2]) {
					$categoryL3[] = $categoryChunks[2];
				}
			}

			$categoryL1 = WHEREFROM_mostPopularInArray($categoryL1);
			$categoryL2 = WHEREFROM_mostPopularInArray($categoryL2);
			$categoryL3 = WHEREFROM_mostPopularInArray($categoryL3);
			
			$productData = array(
				"sku" => $id,
				"name" => $product->get_title(),
				"brandName"=> $brand_name,
				"description" => $product->get_description(),
				"imageUrl"=> wp_get_attachment_url( $product->get_image_id() ),
				"url" => $product->get_permalink(),
				"category1" => $categoryL1,
				"category2" => $categoryL2,
				"category3" => $categoryL3
			);

			$products[] = $productData;
		}

		download_send_headers("wf_export_" . date("Y-m-d") . ".csv");
		echo array2csv($products);
		update_option('wherefrom_last_export_timestamp', time());
		die();
	}
	/**
	 * registers menus
	 */
	public function addPluginAdminMenu() {
		add_menu_page(  'wherefrom', 'Wherefrom', 'administrator', 'wherefrom', array( $this, 'displayPluginAdminDashboard' ), 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjgwIiBoZWlnaHQ9IjY3MyIgdmlld0JveD0iMCAwIDY4MCA2NzMiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0xNjcuMjE5IDU0Mi4yMkw0NS4zMDk1IDEzNC43MkM0NC4xNTgxIDEzMC44NzEgNDcuMDQwNSAxMjcgNTEuMDU3NyAxMjdIMTQ5LjAzNkMxNTEuNjg1IDEyNyAxNTQuMDIxIDEyOC43MzggMTU0Ljc4MyAxMzEuMjc2TDIxNC4zODUgMzI5Ljk1QzIxNi4wMzYgMzM1LjQ1NCAyMjMuNzMzIDMzNS43MTMgMjI1Ljc1IDMzMC4zMzNMMzAwLjU0IDEzMC44OTNDMzAxLjQxOCAxMjguNTUxIDMwMy42NTcgMTI3IDMwNi4xNTggMTI3SDM3OS40MDNDMzgxLjg3NCAxMjcgMzg0LjA5MiAxMjguNTE0IDM4NC45OTIgMTMwLjgxNkw0NjMuMDc4IDMzMC41NzFDNDY1LjE2NCAzMzUuOTA3IDQ3Mi44MjcgMzM1LjU2NCA0NzQuNDI3IDMzMC4wNjNMNTMyLjI0MiAxMzEuMzI0QzUzMi45ODcgMTI4Ljc2MiA1MzUuMzM1IDEyNyA1MzguMDAzIDEyN0g2MjkuMDc5QzYzMy4wNTUgMTI3IDYzNS45MzIgMTMwLjc5NyA2MzQuODU1IDEzNC42MjVMNTIwLjIzMSA1NDIuMTI1QzUxOS41MDMgNTQ0LjcxMiA1MTcuMTQzIDU0Ni41IDUxNC40NTUgNTQ2LjVINDI0LjI1M0M0MjEuNzA1IDU0Ni41IDQxOS40MzUgNTQ0Ljg5MSA0MTguNTkyIDU0Mi40ODdMMzU1LjMwNCAzNjIuMTE4QzM1My40NjggMzU2Ljg4NCAzNDYuMTIyIDM1Ni43MzEgMzQ0LjA2OSAzNjEuODgzTDI3Mi4wMDYgNTQyLjcyMUMyNzEuMDk3IDU0NS4wMDMgMjY4Ljg4OCA1NDYuNSAyNjYuNDMyIDU0Ni41SDE3Mi45NjhDMTcwLjMxNiA1NDYuNSAxNjcuOTc5IDU0NC43NiAxNjcuMjE5IDU0Mi4yMloiIGZpbGw9ImJsYWNrIi8+Cjwvc3ZnPgo=', 26 );
		add_submenu_page( 'wherefrom', 'Wherefrom Settings', 'Settings', 'administrator', 'wherefrom-settings', array( $this, 'displayPluginAdminSettings' ));
	}

	/** 
	 * renders the main page
	 */
	public function displayPluginAdminDashboard() {
		$seoName = get_option('wherefrom_seo_name', false );
		$hasSeoName = $seoName !== false && $seoName !== '';

		$prefix = WherefromUtils::isWooCommerceActive() ? 'wc' : 'wp';

		if (! $hasSeoName) {
			require_once 'partials/setup.php';
			return;
		} else {
			require_once 'partials/'.$prefix.'/main.php';
			return;
		}
  }

	/**
	 * renders settings page
	 */
	public function displayPluginAdminSettings() {
		if(isset($_GET['error_message'])){
			add_action('admin_notices', array($this,'wherefromSettingsMessages'));
			do_action( 'admin_notices', sanitize_text_field($_GET['error_message']) );
		}
		require_once 'partials/settings.php';
	}

	/**
	 * renders settings messages
	 */
	public function wherefromSettingsMessages($error_message){
		switch ($error_message) {
			case '1':
				$message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );                 $err_code = esc_attr( 'wherefrom_seo_name' );                 $setting_field = 'wherefrom_seo_name';                 
				break;
		}
		$type = 'error';
		add_settings_error(
			$setting_field,
			$err_code,
			$message,
			$type
		);
	}

	/**
	 * sets up settings fields
	 */
	public function registerAndBuildFields() {
		add_settings_section(
			'wherefrom_general_section', 
			'',  
			array( $this, 'wherefrom_display_general_account' ),    
			'wherefrom_general_settings'                   
		);

		// ---- seo name
		wherefrom_create_settings_field(
			'wherefrom_seo_name',
			'Wherefrom SEO name',
			array (
				'type'      => 'input',
				'subtype'   => 'text',
				'size'			=> '30',
				'prepend_value' => 'https://www.wherefrom.org/'
			)
		);

		// ---- wc specific
		if (WherefromUtils::isWooCommerceActive()) {
			// if (false) {
				// ---- api key
				wherefrom_create_settings_field(
					'wherefrom_api_key',
					'Wherefrom API key',
					array (
						'type'      => 'input',
						'subtype'   => 'text'
					)
				);
			// }

			// ---- enable widget on product page
			wherefrom_create_settings_field(
				'wherefrom_enable_single_product_widget',
				'Show score on product page',
				array (
					'type'      => 'input',
					'subtype'   => 'checkbox',
				),
				array("default" => true)
			);

			// ---- action 
			wherefrom_create_settings_field(
				'wherefrom_widget_action',
				'Render Wwdget on action',
				array (
					'type'      => 'input',
					'subtype'   => 'text',
					'size'			=> 50,
				),
				array("default" => "woocommerce_single_product_summary")
			);
			
			// ---- widget priority on product page
			wherefrom_create_settings_field(
				'wherefrom_widget_priority',
				'Widget priority',
				array (
					'type'    => 'input',
					'subtype'	=> 'number',
					'min' 		=> 0,
					'step' 		=> 1,
					'mac' 		=> 100
				),
				array("default" => 25)
			);

			// ---- id to use
			wherefrom_create_settings_field(
				'wherefrom_id_field',
				'Product ID Field',
				array (
					'type'    => 'select',
					'options' => array(
						'SKU'			=> 'SKU',
						'ID'			=> 'ID'
					),
				),
				array("default" => 25)
			);

			// ---- categories to exclude
			wherefrom_create_settings_field(
				'wherefrom_categories_to_exclude',
				'Categories to exclude',
				array (
					'type'      	=> 'select',
					'options' 		=> wherefrom_getAllCategories(),
					'multiselect' => true,
					'size' 				=> 20
				)
			);

			// ---- enable autosync
			wherefrom_create_settings_field(
				'wherefrom_enable_product_autosync',
				'Products auto sync',
				array (
					'type'      => 'input',
					'subtype'   => 'checkbox',
				),
				array("default" => true)
			);

			// ---- enable checkout
			// wherefrom_create_settings_field(
			// 	'wherefrom_enable_checkout_review',
			// 	'Request review after chekout',
			// 	array (
			// 		'type'      => 'input',
			// 		'subtype'   => 'checkbox',
			// 	),
			// 	array("default" => true)
			// );
		}
	}

	/**
	 * displays a line of text at the top of settings
	 */
	public function wherefrom_display_general_account() {
		echo '<p>These settings apply to all Wherefrom Plugin functionality.</p>';
	} 
}