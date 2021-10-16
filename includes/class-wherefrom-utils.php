<?php
class WherefromUtils {
  public static function isWooCommerceActive() {
    // Test to see if WooCommerce is active (including network activated).
    $plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

    return in_array( $plugin_path, wp_get_active_and_valid_plugins() );
  }
}
?>