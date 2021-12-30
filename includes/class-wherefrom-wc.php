<?php
  class Wherefrom_WC {
    public function renderProductScore() {
      global $product;

      $id = false;
      
      $idField = get_option('wherefrom_id_field', 'SKU' );
      $enabled =  get_option('wherefrom_enable_single_product_widget', false);
      
      if (! $enabled) return;

      if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) && $idField === 'SKU' ) {
        $id = $product->get_sku();
      } else {
        $id = $product->get_id();
      }

      if ($id) {
        echo do_shortcode('[wherefrom_product_widget id='.esc_attr($id).']');
      }
    }

    public function handleProductUpdated($post_id, $post, $update) {
      if ($post->post_status != 'publish' || $post->post_type != 'product') {
        return;
      }

      if (!$product = wc_get_product( $post )) {
          return;
      }

      $productData = WHEREFROM_buildProduct($product);
      
      $products = array();
      $products[] = $productData;

		  $response = WHEREFROM_postProducts($products);
    }

    public function register() {
      $priority = get_option('wherefrom_widget_priority', 25);
      $action = get_option('wherefrom_widget_action', 'woocommerce_single_product_summary');
      $autoSyncEnabled = get_option('wherefrom_enable_product_autosync', true );

      add_action( $action, array($this, 'renderProductScore'), $priority, 1 );
      if ($autoSyncEnabled) {
        add_action( 'save_post', array($this, 'handleProductUpdated'), 10, 3 );
      }
    }
  }
?>