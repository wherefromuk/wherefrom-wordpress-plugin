<?php

/**
 *
 * @link              https://www.wherefrom.org
 * @package           Wherefrom
 *
 * @wordpress-plugin
 * Plugin Name:       Wherefrom
 * Plugin URI:        https://github.com/wherefromuk/wherefrom-wordpress-plugin
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.1.1
 * Author:            Wherefrom LTD
 * Author URI:        https://www.wherefrom.org/crissmoldovan
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wherefrom
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WHEREFROM_VERSION', '1.1.1' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wherefrom.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.1
 */
function run_wherefrom() {

	$plugin = new Wherefrom();
	$plugin->run();

}
run_wherefrom();
