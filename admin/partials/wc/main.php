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
$apiKey = get_option('wherefrom_api_key');
$autoSyncEnabled = get_option('wherefrom_enable_product_autosync', true );
?>
<div class="wf-wrapper">
  <div class="wf-header">
    <img src="<?php echo plugin_dir_url( __FILE__ ) . '../../img/logo.png' ?>" width="48px" alt="wherefrom logo" />
  </div>
</div>

<div class="wf-content">
  <h1>Wherefrom + WooCommerce = ❤️</h1>
  <p>Your woocommerce store is now connected to wherefrom</p>
  <?php if ($autoSyncEnabled) { ?>
    <p><b>Product Autosync is enabled.</b> <br />When a new product is creaded, or one is updated, it will be automatically submited to Wherefrom.</p>
    <p>If you would like wherefrom to reprocess your products, you can do this, by syncing your whole catalog of products</p>
  <?php } ?>
  <p>
    <?php if ($lastExportTimeStamp) { ?>
    <!-- <div class="row" style="margin-bottom: 20px;">
      Products to include in CSV:&nbsp;
      <select id="products_to_include">
        <option value="new-products" selected>Products created after last export (<?=date('m/d/Y H:i:s', $lastExportTimeStamp)?>)</option>
        <option value="all">All products</option>
      </select>
    </div> -->
    <?php } ?>
    <div class="row">
      <!-- <button class="green">Sync products</button>  -->
      <?php if ($apiKey && $apiKey !== '') { ?>
        <?php if ($autoSyncEnabled)  { ?>
          <button id="wc-sync-products" class="green">Sync Now</button></div>
          <div id="wf-sync-products-spinner" class="lds-ring" style="margin-top: 10px; visibility: hidden;"><div></div><div></div><div></div><div></div></div>
        <?php } else { ?>
          <button id="wc-sync-products" class="green">Sync Products</button></div>
          <div id="wf-sync-products-spinner" class="lds-ring" style="margin-top: 10px; visibility: hidden;"><div></div><div></div><div></div><div></div></div>
        <?php } ?>
      <?php } else { ?>
        <button id="wc-generate-products-csv" class="green">Generate CSV</button></div>
        <p>Please add an Api Key in the Settings section to enable auto-publishing</p>
      <?php } ?>
  </p>
  <p>You can find more details on the <a href="https://docs.wherefrom.org/wordpress-plugin#woocommerce-integration" target="_blank">Wherefrom Docs</a> page.</p>
  <p>Alternatively please feel free to email us at <a href="mailto: support@wherefrom.org">support@wherefrom.org</a></p>
</div>



