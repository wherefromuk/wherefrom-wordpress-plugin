<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.wherefrom.org
 *
 * @package    Wherefrom
 * @subpackage Wherefrom/admin/partials
 */

$lastExportTimeStamp = get_option('wherefrom_last_export_timestamp', null );
?>

<div class="wf-wrapper">
  <div class="wf-header">
    <img src="<?php echo plugin_dir_url( __FILE__ ) . '../../img/logo.png' ?>" width="48px" alt="wherefrom logo" />
  </div>
</div>

<div class="wf-content">
  <h1>Wherefrom + WooCommerce = ❤️</h1>
  <p>Your woocommerce store is now connected to wherefrom</p>
  <p>
    <?php if ($lastExportTimeStamp) { ?>
    <div class="row" style="margin-bottom: 20px;">
      Products to include in CSV:&nbsp;
      <select id="products_to_include">
        <option value="new-products" selected>Products created after last export (<?=date('m/d/Y H:i:s', $lastExportTimeStamp)?>)</option>
        <option value="all">All products</option>
      </select>
    </div>
    <?php } ?>
    <div class="row">
      <!-- <button class="green">Sync products</button>  -->
      <button id="wc-generate-products-csv" class="green">Export CSV</button></div>
  </p>
  <p>You can find more details on the <a href="https://docs.wherefrom.org/wordpress-plugin#woocommerce-integration" target="_blank">Wherefrom Docs</a> page.</p>
  <p>Alternatively please feel free to email us at <a href="mailto: support@wherefrom.org">support@wherefrom.org</a></p>
</div>



